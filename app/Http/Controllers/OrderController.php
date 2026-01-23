<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Lead;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderConfirmedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view orders')->only(['index', 'show', 'downloadInvoice']);
        $this->middleware('permission:create orders')->only(['selectLead', 'create', 'store']);
        $this->middleware('permission:edit orders')->only(['edit', 'update']);
        $this->middleware('permission:delete orders')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $orders = Order::with(['lead', 'items'])
            ->when($request->status, fn($q) => $q->where('order_status', $request->status))
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('id', 'like', "%{$request->search}%")
                        ->orWhereHas('lead', function ($subQuery) use ($request) {
                            $subQuery->where('customer_name', 'like', "%{$request->search}%")
                                ->orWhere('phone', 'like', "%{$request->search}%");
                        });
                });
            })
            ->latest()
            ->paginate(20);

        return view('orders.index', compact('orders'));
    }

    public function selectLead(Request $request): View
    {
        $leads = Lead::when($request->search, function ($q) use ($request) {
            $q->where('customer_name', 'like', "%{$request->search}%")
              ->orWhere('phone', 'like', "%{$request->search}%")
              ->orWhere('lead_code', 'like', "%{$request->search}%");
        })->latest()->paginate(20);

        return view('orders.select_lead', compact('leads'));
    }

    public function create(Lead $lead): View
    {
        $products = Product::where('is_active', true)->get();
        return view('orders.create', compact('lead', 'products'));
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, &$order) {
                $order = Order::create([
                    'lead_id' => $request->lead_id,
                    'payment_method' => $request->payment_method,
                    'order_status' => $request->order_status,
                    'notes' => $request->notes,
                ]);

                foreach ($request->items as $item) {
                    $product = isset($item['product_id']) ? Product::find($item['product_id']) : null;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'] ?? null,
                        'product_name' => $product ? $product->name : $item['product_name'],
                        'variant' => $item['variant'] ?? null,
                        'size' => $item['size'] ?? null,
                        'color' => $item['color'] ?? null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                    ]);
                }

                if ($order->order_status === 'Confirmed') {
                    $admins = User::role('admin')->get();
                    \Illuminate\Support\Facades\Log::info('Order Confirmed. Admins found: ' . $admins->count());
                    if ($admins->count() > 0) {
                        Notification::send($admins, new OrderConfirmedNotification($order));
                    }
                }

                \App\Services\ActivityLogger::log('تم إنشاء طلب جديد', $order);
            });

            return redirect()->route('leads.show', $order->lead_id)
                ->with('success', 'تمت إضافة الطلب بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حفظ الطلب: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Order $order): View
    {
        $order->load(['lead', 'items']);
        $activities = $order->activities()->with('causer')->paginate(10);

        return view('orders.show', compact('order', 'activities'));
    }

    public function edit(Order $order): View
    {
        $order->load('items.product');
        $products = Product::where('is_active', true)->get();

        return view('orders.edit', compact('order', 'products'));
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $order) {
                $originalStatus = $order->order_status;

                $order->update([
                    'payment_method' => $request->payment_method,
                    'order_status' => $request->order_status,
                    'notes' => $request->notes,
                ]);

                // TODO: Refactor to sync items instead of delete-all to preserve history/IDs
                $order->items()->delete();

                foreach ($request->items as $item) {
                    $product = isset($item['product_id']) ? Product::find($item['product_id']) : null;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'] ?? null,
                        'product_name' => $product ? $product->name : $item['product_name'],
                        'variant' => $item['variant'] ?? null,
                        'size' => $item['size'] ?? null,
                        'color' => $item['color'] ?? null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                    ]);
                }

                if ($originalStatus !== 'Confirmed' && $request->order_status === 'Confirmed') {
                    $admins = User::role('admin')->get();
                    if ($admins->count() > 0) {
                        Notification::send($admins, new OrderConfirmedNotification($order));
                    }
                }

                \App\Services\ActivityLogger::log('تم تحديث الطلب', $order, ['changes' => $order->getChanges()]);
            });

            return redirect()->route('leads.show', $order->lead_id)
                ->with('success', 'تم تحديث الطلب بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحديث الطلب: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Order $order): RedirectResponse
    {
        $leadId = $order->lead_id;
        $order->delete();

        \App\Services\ActivityLogger::log('تم حذف الطلب', $order);

        return redirect()->route('leads.show', $leadId)
            ->with('success', 'تم حذف الطلب بنجاح');
    }

    public function downloadInvoice(Order $order)
    {
        $order->load(['lead', 'items']);
        
        $pdf = Pdf::loadView('orders.invoice', compact('order'));
        
        return $pdf->download('invoice-' . $order->id . '.pdf');
    }
}
