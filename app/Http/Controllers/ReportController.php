<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view reports');
    }

    public function exportLeads(Request $request)
    {
        $fileName = 'leads_export_' . date('Y-m-d_H-i-s') . '.csv';

        return new StreamedResponse(function () use ($request) {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // Header
            fputcsv($handle, [
                'ID',
                'الكود',
                'الاسم',
                'الهاتف',
                'البريد الإلكتروني',
                'الحالة',
                'المصدر',
                'القيمة المتوقعة',
                'المدينة',
                'تاريخ الإنشاء',
            ]);

            $query = Lead::query();

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('lead_code', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('platform')) {
                $query->whereHas('campaign', function($q) use ($request) {
                    $q->where('platform', $request->platform);
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $query->chunk(100, function ($leads) use ($handle) {
                foreach ($leads as $lead) {
                    fputcsv($handle, [
                        $lead->id,
                        $lead->lead_code,
                        $lead->customer_name,
                        $lead->phone,
                        $lead->email,
                        __('leads.status.' . $lead->status),
                        $lead->campaign ? $lead->campaign->name : 'مباشر',
                        $lead->expected_value,
                        $lead->city,
                        $lead->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function exportOrders(Request $request)
    {
        $fileName = 'orders_export_' . date('Y-m-d_H-i-s') . '.csv';

        return new StreamedResponse(function () use ($request) {
            $handle = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // Header
            fputcsv($handle, [
                'رقم الطلب',
                'العميل',
                'طريقة الدفع',
                'حالة الطلب',
                'إجمالي الطلب',
                'تاريخ الطلب',
            ]);

            $query = Order::with(['lead', 'items']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('order_status', $request->status);
            }

            if ($request->filled('campaign_id')) {
                $query->whereHas('lead', function($q) use ($request) {
                    $q->where('campaign_id', $request->campaign_id);
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $query->chunk(100, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    fputcsv($handle, [
                        $order->id,
                        $order->lead ? $order->lead->customer_name : '-',
                        __('orders.payment_method.' . $order->payment_method),
                        __('orders.status.' . $order->order_status),
                        $order->total_amount,
                        $order->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
