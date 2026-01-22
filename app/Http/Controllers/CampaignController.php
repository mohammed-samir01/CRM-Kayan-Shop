<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(): View
    {
        $campaigns = Campaign::withCount('leads')->latest()->paginate(20);

        return view('campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        return view('campaigns.create');
    }

    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        $campaign = Campaign::create($request->validated());

        \App\Services\ActivityLogger::log('تم إنشاء حملة جديدة', $campaign);

        return redirect()->route('campaigns.index')
            ->with('success', 'تمت إضافة الحملة بنجاح');
    }

    public function show(Campaign $campaign): View
    {
        $campaign->load('leads');
        $activities = $campaign->activities()->with('causer')->paginate(10);

        return view('campaigns.show', compact('campaign', 'activities'));
    }

    public function edit(Campaign $campaign): View
    {
        return view('campaigns.edit', compact('campaign'));
    }

    public function update(UpdateCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        $campaign->update($request->validated());

        \App\Services\ActivityLogger::log('تم تحديث بيانات الحملة', $campaign, ['changes' => $campaign->getChanges()]);

        return redirect()->route('campaigns.index')
            ->with('success', 'تم تحديث الحملة بنجاح');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $this->authorize('delete', $campaign);

        $campaign->delete();
        
        \App\Services\ActivityLogger::log('تم حذف الحملة', $campaign);

        return redirect()->route('campaigns.index')
            ->with('success', 'تم حذف الحملة بنجاح');
    }
}
