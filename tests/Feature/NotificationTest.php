<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Order;
use App\Models\User;
use App\Notifications\FollowUpReminderNotification;
use App\Notifications\NewLeadNotification;
use App\Notifications\OrderConfirmedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear Spatie cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'agent']);
        Role::create(['name' => 'manager']);
    }

    public function test_assigned_staff_receives_notification_on_new_lead()
    {
        Notification::fake();

        $staff = User::factory()->create();
        $staff->assignRole('agent'); // Using Spatie role
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $response = $this->post(route('leads.store'), [
            'customer_name' => 'John Doe',
            'phone' => '1234567890',
            'status' => 'New',
            'assigned_to' => $staff->id,
            'campaign_id' => \App\Models\Campaign::factory()->create()->id,
        ]);

        Notification::assertSentTo($staff, NewLeadNotification::class);
    }

    public function test_admins_receive_notification_on_unassigned_lead()
    {
        Notification::fake();

        $admin1 = User::factory()->create();
        $admin1->assignRole('admin');
        
        $admin2 = User::factory()->create();
        $admin2->assignRole('admin');

        $staff = User::factory()->create();
        $staff->assignRole('agent');

        $this->actingAs($staff);

        $response = $this->post(route('leads.store'), [
            'customer_name' => 'Jane Doe',
            'phone' => '0987654321',
            'status' => 'New',
            'assigned_to' => null,
            'campaign_id' => \App\Models\Campaign::factory()->create()->id,
        ]);

        Notification::assertSentTo([$admin1, $admin2], NewLeadNotification::class);
    }

    public function test_admin_receives_notification_on_confirmed_order()
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $staff = User::factory()->create();
        $staff->assignRole('agent');
        
        $lead = Lead::factory()->create();

        $this->actingAs($staff);

        $response = $this->post(route('orders.store'), [
            'lead_id' => $lead->id,
            'order_status' => 'Confirmed',
            'payment_method' => 'Cash',
            'total_value' => 1000,
            'payment_status' => 'Paid',
            'items' => [
                ['product_name' => 'Item 1', 'variant' => 'Size M', 'quantity' => 1, 'unit_price' => 1000]
            ]
        ]);

        Notification::assertSentTo($admin, OrderConfirmedNotification::class);
    }

    public function test_no_notification_on_order_update_without_status_change_to_confirmed()
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $lead = Lead::factory()->create();
        $order = Order::factory()->create(['lead_id' => $lead->id, 'order_status' => 'Pending']);

        $this->actingAs($admin);

        // Update without changing status to Confirmed
        $this->put(route('orders.update', $order), [
            'lead_id' => $lead->id,
            'order_status' => 'Pending',
            'payment_method' => 'Cash',
            'items' => [['product_name' => 'Item 1', 'variant' => 'Default', 'quantity' => 1, 'unit_price' => 100]]
        ]);

        Notification::assertNotSentTo($admin, OrderConfirmedNotification::class);

        // Update to Confirmed
        $response = $this->put(route('orders.update', $order), [
            'lead_id' => $lead->id,
            'order_status' => 'Confirmed',
            'payment_method' => 'Cash',
            'items' => [['product_name' => 'Item 1', 'variant' => 'Default', 'quantity' => 1, 'unit_price' => 100]]
        ]);
        
        $response->assertSessionHasNoErrors();

        Notification::assertSentTo($admin, OrderConfirmedNotification::class);

        // Update again (already Confirmed)
        Notification::fake(); // Reset
        $this->put(route('orders.update', $order), [
            'lead_id' => $lead->id,
            'order_status' => 'Confirmed',
            'payment_method' => 'Cash',
            'items' => [['product_name' => 'Item 1', 'variant' => 'Default', 'quantity' => 1, 'unit_price' => 100]]
        ]);

        Notification::assertNotSentTo($admin, OrderConfirmedNotification::class);
    }


    public function test_follow_up_reminder_command_sends_notifications()
    {
        Notification::fake();

        $staff = User::factory()->create(['role' => 'staff']);
        
        // Lead with follow up date today
        Lead::factory()->create([
            'assigned_to' => $staff->id,
            'follow_up_date' => now()->toDateString(),
        ]);

        // Lead with follow up date tomorrow (should not notify)
        Lead::factory()->create([
            'assigned_to' => $staff->id,
            'follow_up_date' => now()->addDay()->toDateString(),
        ]);

        $this->artisan('leads:send-reminders')
             ->assertExitCode(0);

        Notification::assertSentTo($staff, FollowUpReminderNotification::class, function ($notification, $channels, $notifiable) {
            return $notification->lead->follow_up_date->isToday();
        });
        
        // Ensure only 1 notification sent (for the today one)
        Notification::assertSentToTimes($staff, FollowUpReminderNotification::class, 1);
    }

    public function test_user_can_mark_notification_as_read()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create();
        
        $notification = new NewLeadNotification($lead);
        $user->notify($notification);

        $dbNotification = $user->notifications()->first();
        
        $this->assertNull($dbNotification->read_at);

        $this->actingAs($user)
             ->get(route('notifications.read', $dbNotification->id))
             ->assertRedirect(route('leads.show', $lead->id));

        $this->assertNotNull($dbNotification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create();
        
        $user->notify(new NewLeadNotification($lead));
        $user->notify(new NewLeadNotification($lead));

        $this->assertEquals(2, $user->unreadNotifications()->count());

        $this->actingAs($user)
             ->post(route('notifications.readAll'))
             ->assertRedirect();

        $this->assertEquals(0, $user->unreadNotifications()->count());
    }
}
