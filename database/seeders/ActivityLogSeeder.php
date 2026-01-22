<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Campaign;
use App\Models\Lead;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            return;
        }

        // Campaigns Activities
        $campaigns = Campaign::latest()->take(5)->get();
        foreach ($campaigns as $campaign) {
            ActivityLog::create([
                'description' => 'تم إنشاء الحملة',
                'subject_type' => Campaign::class,
                'subject_id' => $campaign->id,
                'causer_type' => User::class,
                'causer_id' => $users->random()->id,
                'properties' => ['old' => [], 'attributes' => $campaign->toArray()],
            ]);
        }

        // Leads Activities
        $leads = Lead::latest()->take(10)->get();
        foreach ($leads as $lead) {
            // Created activity
            ActivityLog::create([
                'description' => 'تم إنشاء العميل المحتمل',
                'subject_type' => Lead::class,
                'subject_id' => $lead->id,
                'causer_type' => User::class,
                'causer_id' => $users->random()->id,
                'properties' => ['attributes' => $lead->toArray()],
            ]);

            // Status update activity (simulation)
            ActivityLog::create([
                'description' => 'تم تحديث حالة العميل',
                'subject_type' => Lead::class,
                'subject_id' => $lead->id,
                'causer_type' => User::class,
                'causer_id' => $users->random()->id,
                'properties' => [
                    'old' => ['status' => 'new'], 
                    'attributes' => ['status' => $lead->status]
                ],
            ]);
        }

        // Orders Activities
        $orders = Order::latest()->take(10)->get();
        foreach ($orders as $order) {
            ActivityLog::create([
                'description' => 'تم إنشاء الطلب',
                'subject_type' => Order::class,
                'subject_id' => $order->id,
                'causer_type' => User::class,
                'causer_id' => $users->random()->id,
                'properties' => ['attributes' => $order->toArray()],
            ]);
            
            if ($order->status !== 'pending') {
                ActivityLog::create([
                    'description' => "تم تحديث حالة الطلب إلى {$order->status}",
                    'subject_type' => Order::class,
                    'subject_id' => $order->id,
                    'causer_type' => User::class,
                    'causer_id' => $users->random()->id,
                    'properties' => [
                        'old' => ['status' => 'pending'],
                        'attributes' => ['status' => $order->status]
                    ],
                ]);
            }
        }
    }
}
