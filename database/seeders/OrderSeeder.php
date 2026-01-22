<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        
        if ($products->isEmpty()) {
            return;
        }

        // Get leads that are Confirmed or Shipped to create orders for them
        $leads = Lead::whereIn('status', ['Confirmed', 'Shipped'])->get();

        foreach ($leads as $lead) {
            $order = Order::factory()->create([
                'lead_id' => $lead->id,
                'order_status' => $lead->status, // Sync order status with lead status
                'total_value' => 0, // Will calculate after adding items
            ]);

            $totalValue = 0;
            $itemsCount = rand(1, 3);

            for ($i = 0; $i < $itemsCount; $i++) {
                $product = $products->random();
                $quantity = rand(1, 5);
                $unitPrice = $product->price;
                $lineTotal = $quantity * $unitPrice;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'variant' => 'Default',
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);

                $totalValue += $lineTotal;
            }

            $order->update(['total_value' => $totalValue]);
        }
    }
}
