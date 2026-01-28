<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view leads')->only(['index', 'show']);
        $this->middleware('permission:create leads')->only(['create', 'store']);
        $this->middleware('permission:edit leads')->only(['edit', 'update']);
        $this->middleware('permission:delete leads')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $query = Lead::with(['campaign', 'assignedTo']);

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('lead_code', 'like', "%{$search}%");
            });
        }

        if ($request->platform) {
            $query->whereHas('campaign', function ($q) use ($request) {
                $q->where('platform', $request->platform);
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $leads = $query->latest()->paginate(20);

        return view('leads.index', compact('leads'));
    }

    public function create(): View
    {
        return view('leads.create');
    }

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $lead = Lead::create($request->validated());

        \App\Services\ActivityLogger::log('تم إنشاء عميل جديد', $lead);

        if ($lead->assignedTo) {
            $lead->assignedTo->notify(new \App\Notifications\NewLeadNotification($lead));
        } else {
            // Notify all admins if no one is assigned
            $admins = \App\Models\User::role('admin')->get();
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\NewLeadNotification($lead));
        }

        return redirect()->route('leads.index')
            ->with('success', 'تمت إضافة العميل المتوقع بنجاح');
    }

    public function show(Lead $lead): View
    {
        $lead->load(['campaign', 'assignedTo', 'orders.items']);
        $activities = $lead->activities()->with('causer')->latest()->paginate(10);

        return view('leads.show', compact('lead', 'activities'));
    }

    public function edit(Lead $lead): View
    {
        return view('leads.edit', compact('lead'));
    }

    public function update(UpdateLeadRequest $request, Lead $lead): RedirectResponse
    {
        $lead->update($request->validated());
        
        \App\Services\ActivityLogger::log('تم تحديث بيانات العميل', $lead, ['changes' => $lead->getChanges()]);

        return redirect()->route('leads.show', $lead)
            ->with('success', 'تم تحديث بيانات العميل المتوقع بنجاح');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        \App\Services\ActivityLogger::log('تم حذف العميل', $lead);

        return redirect()->route('leads.index')
            ->with('success', 'تم حذف العميل المتوقع بنجاح');
    }
}
