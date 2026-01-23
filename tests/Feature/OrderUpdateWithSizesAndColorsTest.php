<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderUpdateWithSizesAndColorsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->user = User::factory()->create();
        $role = Role::create(['name' => 'admin']);
        $this->user->assignRole($role);
    }

    public function test_user_can_update_order_with_sizes_and_colors()
    {
        $this->actingAs($this->user);

        $lead = Lead::factory()->create();
        $product = Product::factory()->create([
            'sizes' => ['Small', 'Medium'],
            'colors' => ['Red', 'Blue'],
            'price' => 100
        ]);

        $order = Order::create([
            'lead_id' => $lead->id,
            'payment_method' => 'Cash',
            'order_status' => 'Pending',
        ]);

        // Create initial item
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'size' => 'Small',
            'color' => 'Red',
            'quantity' => 1,
            'unit_price' => 100,
        ]);

        // Update data
        $updateData = [
            'payment_method' => 'Cash',
            'order_status' => 'Confirmed',
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'size' => 'Medium',
                    'color' => 'Blue',
                    'quantity' => 2,
                    'unit_price' => 100,
                ]
            ],
        ];

        $response = $this->put(route('orders.update', $order), $updateData);

        $response->assertRedirect(route('leads.show', $lead->id));
        $response->assertSessionHas('success');

        // Verify database updated
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'size' => 'Medium',
            'color' => 'Blue',
            'quantity' => 2,
        ]);
        
        // Verify old item is gone (since we replaced it)
        $this->assertDatabaseMissing('order_items', [
            'order_id' => $order->id,
            'size' => 'Small',
            'color' => 'Red',
        ]);
    }
}
