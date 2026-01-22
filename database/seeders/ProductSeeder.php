<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Premium CRM License',
                'sku' => 'CRM-001',
                'price' => 999.00,
                'stock' => 100,
                'description' => 'Annual license for the CRM software',
            ],
            [
                'name' => 'Consultation Hour',
                'sku' => 'SRV-001',
                'price' => 150.00,
                'stock' => 1000,
                'description' => 'One hour of technical consultation',
            ],
            [
                'name' => 'Custom Integration',
                'sku' => 'DEV-001',
                'price' => 500.00,
                'stock' => 50,
                'description' => 'Custom API integration service',
            ],
            [
                'name' => 'Starter Package',
                'sku' => 'PKG-001',
                'price' => 299.00,
                'stock' => 200,
                'description' => 'Basic setup and onboarding',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
