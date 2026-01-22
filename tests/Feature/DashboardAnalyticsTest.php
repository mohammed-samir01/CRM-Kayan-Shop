<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_top_selling_products()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create();
        
        // Create products
        $product1 = Product::create(['name' => 'P1', 'price' => 10, 'stock' => 100]);
        $product2 = Product::create(['name' => 'P2', 'price' => 20, 'stock' => 100]);

        // Create orders with items
        $order = Order::create([
            'lead_id' => $lead->id,
            'payment_method' => 'Cash',
            'order_status' => 'Confirmed',
            'total_value' => 100,
            'order_number' => 'ORD-001'
        ]);

        // Product 1 sold 5 times
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'product_name' => $product1->name,
            'variant' => 'Default',
            'quantity' => 5,
            'unit_price' => 10,
            'line_total' => 50
        ]);

        // Product 2 sold 2 times
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'product_name' => $product2->name,
            'variant' => 'Default',
            'quantity' => 2,
            'unit_price' => 20,
            'line_total' => 40
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('topProducts', function ($topProducts) use ($product1, $product2) {
            return $topProducts->first()->product_id === $product1->id &&
                   $topProducts->first()->total_sold == 5;
        });
    }

    public function test_dashboard_displays_low_stock_alerts()
    {
        $user = User::factory()->create();
        
        // Create products with different stock levels
        $lowStockProduct = Product::create(['name' => 'Low Stock', 'price' => 10, 'stock' => 5]);
        $highStockProduct = Product::create(['name' => 'High Stock', 'price' => 20, 'stock' => 50]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('lowStockProducts', function ($lowStockProducts) use ($lowStockProduct, $highStockProduct) {
            return $lowStockProducts->contains('id', $lowStockProduct->id) &&
                   !$lowStockProducts->contains('id', $highStockProduct->id);
        });
    }

    public function test_dashboard_handles_soft_deleted_products_in_top_selling()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create();
        
        $product = Product::create(['name' => 'Soft Deleted Product', 'price' => 10, 'stock' => 100]);
        
        $order = Order::create([
            'lead_id' => $lead->id,
            'payment_method' => 'Cash',
            'order_status' => 'Confirmed',
            'total_value' => 50,
            'order_number' => 'ORD-SD-001'
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'variant' => 'Default',
            'quantity' => 5,
            'unit_price' => 10,
            'line_total' => 50
        ]);

        $product->delete(); // Soft delete

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Soft Deleted Product');
    }

    public function test_dashboard_handles_force_deleted_products_in_top_selling()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create();
        
        $product = Product::create(['name' => 'Force Deleted Product', 'price' => 10, 'stock' => 100]);
        
        $order = Order::create([
            'lead_id' => $lead->id,
            'payment_method' => 'Cash',
            'order_status' => 'Confirmed',
            'total_value' => 50,
            'order_number' => 'ORD-FD-001'
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'variant' => 'Default',
            'quantity' => 5,
            'unit_price' => 10,
            'line_total' => 50
        ]);

        $product->forceDelete(); // Force delete

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('منتج غير موجود');
    }
}
