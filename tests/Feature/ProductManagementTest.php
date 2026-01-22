<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_products_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('products.index');
    }

    public function test_user_can_create_product()
    {
        $user = User::factory()->create();

        $productData = [
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 100.50,
            'stock' => 10,
            'description' => 'Test Description',
            'is_active' => '1',
        ];

        $response = $this->actingAs($user)->post(route('products.store'), $productData);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 100.50,
            'is_active' => true,
        ]);
    }

    public function test_user_can_update_product()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Old Name',
            'price' => 50,
            'stock' => 5,
            'is_active' => true
        ]);

        $updateData = [
            'name' => 'New Name',
            'sku' => 'UPDATED-SKU',
            'price' => 75.00,
            'stock' => 20,
            'description' => 'Updated Description',
            'is_active' => '1',
        ];

        $response = $this->actingAs($user)->put(route('products.update', $product), $updateData);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Name',
            'price' => 75.00,
        ]);
    }

    public function test_user_can_delete_product()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'To Delete',
            'price' => 10,
            'stock' => 1,
            'is_active' => true
        ]);

        $response = $this->actingAs($user)->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_order_creation_uses_product()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create();
        $product = Product::create([
            'name' => 'Order Product',
            'price' => 200,
            'stock' => 100,
            'is_active' => true
        ]);

        $orderData = [
            'lead_id' => $lead->id,
            'payment_method' => 'Cash',
            'order_status' => 'Pending',
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name, // Should be optional if logic is correct, but request requires it currently
                    'variant' => 'Default',
                    'quantity' => 2,
                    'unit_price' => 200,
                ]
            ]
        ];

        $response = $this->actingAs($user)->post(route('orders.store'), $orderData);

        $response->assertRedirect(route('leads.show', $lead));
        
        $this->assertDatabaseHas('orders', [
            'lead_id' => $lead->id,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'product_name' => 'Order Product',
            'unit_price' => 200,
            'quantity' => 2,
        ]);
    }
}
