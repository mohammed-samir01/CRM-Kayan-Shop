<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_orders_by_id()
    {
        $user = User::factory()->create();
        $order1 = Order::factory()->create(['id' => 12345]);
        $order2 = Order::factory()->create(['id' => 67890]);

        $response = $this->actingAs($user)->get(route('orders.index', ['search' => '12345']));

        $response->assertOk();
        $response->assertSee('#12345');
        $response->assertDontSee('#67890');
    }

    public function test_can_search_orders_by_customer_name()
    {
        $user = User::factory()->create();
        $lead1 = Lead::factory()->create(['customer_name' => 'Ahmed Ali']);
        $lead2 = Lead::factory()->create(['customer_name' => 'John Doe']);
        
        $order1 = Order::factory()->create(['lead_id' => $lead1->id]);
        $order2 = Order::factory()->create(['lead_id' => $lead2->id]);

        $response = $this->actingAs($user)->get(route('orders.index', ['search' => 'Ahmed']));

        $response->assertOk();
        $response->assertSee('Ahmed Ali');
        $response->assertDontSee('John Doe');
    }

    public function test_can_search_orders_by_phone()
    {
        $user = User::factory()->create();
        $lead1 = Lead::factory()->create(['phone' => '0501234567']);
        $lead2 = Lead::factory()->create(['phone' => '0509876543']);
        
        $order1 = Order::factory()->create(['lead_id' => $lead1->id]);
        $order2 = Order::factory()->create(['lead_id' => $lead2->id]);

        $response = $this->actingAs($user)->get(route('orders.index', ['search' => '0501234567']));

        $response->assertOk();
        $response->assertSee('0501234567'); // Assuming phone is displayed or linked
        // Actually, phone might not be displayed in the main table, but the lead name should be.
        // Let's verify we see the lead associated with that phone.
        $response->assertSee($lead1->customer_name);
        $response->assertDontSee($lead2->customer_name);
    }
    
    public function test_can_filter_orders_by_status()
    {
        $user = User::factory()->create();
        $order1 = Order::factory()->create(['order_status' => 'Pending']);
        $order2 = Order::factory()->create(['order_status' => 'Shipped']);

        $response = $this->actingAs($user)->get(route('orders.index', ['status' => 'Pending']));

        $response->assertOk();
        // Since the status text is translated, we should look for what's rendered.
        // Or we can check if the order ID is present/absent.
        $response->assertSee('#' . $order1->id);
        $response->assertDontSee('#' . $order2->id);
    }
}
