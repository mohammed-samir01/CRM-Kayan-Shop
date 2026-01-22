<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewLeadNotification;
use App\Notifications\OrderConfirmedNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Notification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $leads = Lead::all();
        $orders = Order::all();

        if ($users->isEmpty() || $leads->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            // Send 3 New Lead Notifications
            $randomLeads = $leads->random(min(3, $leads->count()));
            foreach ($randomLeads as $lead) {
                $user->notify(new NewLeadNotification($lead));
            }

            // Send 2 Order Confirmed Notifications if orders exist
            if ($orders->isNotEmpty()) {
                $randomOrders = $orders->random(min(2, $orders->count()));
                foreach ($randomOrders as $order) {
                    $user->notify(new OrderConfirmedNotification($order));
                }
            }
        }
    }
}
