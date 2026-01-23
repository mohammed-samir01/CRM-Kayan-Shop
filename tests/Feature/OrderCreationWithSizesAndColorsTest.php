<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCreationWithSizesAndColorsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_user_can_create_order_with_sizes_and_colors()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['create orders', 'view orders', 'view leads']);

        $lead = Lead::factory()->create();
        $product = Product::factory()->create([
            'sizes' => ['Small', 'Medium'],
            'colors' => ['Red', 'Blue'],
            'price' => 100
        ]);

        $orderData = [
            'lead_id' => $lead->id,
            'payment_method' => 'Cash',
            'order_status' => 'Pending',
            'notes' => 'Test Order',
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'size' => 'Small',
                    'color' => 'Red',
                    'quantity' => 1,
                    'unit_price' => 100,
                ]
            ],
        ];

        $response = $this->actingAs($user)->post(route('orders.store'), $orderData);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'size' => 'Small',
            'color' => 'Red',
        ]);
    }

    public function test_user_can_create_order_without_sizes_and_colors()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['create orders', 'view orders', 'view leads']);

        $lead = Lead::factory()->create();
        $product = Product::factory()->create([
            'price' => 100
        ]);

        $orderData = [
            'lead_id' => $lead->id,
            'payment_method' => 'Cash',
            'order_status' => 'Pending',
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => 2,
                    'unit_price' => 100,
                ]
            ],
        ];

        $response = $this->actingAs($user)->post(route('orders.store'), $orderData);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'size' => null,
            'color' => null,
        ]);
    }
}
