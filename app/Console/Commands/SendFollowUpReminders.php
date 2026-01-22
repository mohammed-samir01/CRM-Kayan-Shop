<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Notifications\FollowUpReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendFollowUpReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for leads with follow-up date today';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leads = Lead::whereDate('follow_up_date', Carbon::today())
                     ->whereNotNull('assigned_to')
                     ->with('assignedTo')
                     ->get();

        $count = 0;

        foreach ($leads as $lead) {
            if ($lead->assignedTo) {
                // Prevent duplicate notifications for the same day
                $cacheKey = "lead_reminder_{$lead->id}_" . Carbon::today()->toDateString();
                
                if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                    $lead->assignedTo->notify(new FollowUpReminderNotification($lead));
                    \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addDay());
                    $count++;
                }
            }
        }

        $this->info("Sent {$count} follow-up reminders.");

        return 0;
    }
}
