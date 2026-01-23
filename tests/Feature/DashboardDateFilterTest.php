<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardDateFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_filters_stats_by_date_range()
    {
        Carbon::setTestNow('2023-01-01 12:00:00');
        
        // Allow mass assignment of created_at
        Order::unguard();
        Lead::unguard();

        $user = User::factory()->create();
        $this->actingAs($user);

        // Create data for Today
        Lead::factory()->create(['created_at' => now()]);
        
        Order::create([
            'lead_id' => 1,
            'total_value' => 100,
            'order_status' => 'Confirmed',
            'payment_method' => 'Cash',
            'created_at' => now(),
        ]);

        // Create data for Last Month (35 days ago)
        Lead::factory()->create(['created_at' => now()->subDays(35)]);
        
        Order::create([
            'lead_id' => 2,
            'total_value' => 200,
            'order_status' => 'Confirmed',
            'payment_method' => 'Cash',
            'created_at' => now()->subDays(35),
        ]);

        Order::reguard();
        Lead::reguard();

        // 1. Test Default (Last 30 Days) -> Should see Today's data, NOT old data
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('leadsCount', 1);
        $response->assertViewHas('revenue', 100);

        // 2. Test "Last 60 Days" -> Should see BOTH
        $response = $this->get(route('dashboard', ['date_range' => 'last_60_days']));
        $response->assertStatus(200);
        $response->assertViewHas('leadsCount', 2);
        $response->assertViewHas('revenue', 300);

        // 4. Custom Range
        $customStart = now()->subDays(40)->format('Y-m-d');
        $customEnd = now()->subDays(30)->format('Y-m-d');
        
        $response = $this->get(route('dashboard', [
            'date_range' => 'custom',
            'start_date' => $customStart,
            'end_date' => $customEnd
        ]));
        
        $response->assertStatus(200);
        // Should include the "35 days ago" lead/order created above.
        // Should exclude "Today".
        $response->assertViewHas('leadsCount', 1);
        $response->assertViewHas('revenue', 200);
    }
}
