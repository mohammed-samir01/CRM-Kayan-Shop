<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSizeColorTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_user_can_create_product_with_sizes_and_colors()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['view products', 'create products']);

        $productData = [
            'name' => 'T-Shirt',
            'sku' => 'TSHIRT-001',
            'price' => 50.00,
            'stock' => 100,
            'description' => 'Cotton T-Shirt',
            'is_active' => '1',
            'sizes' => ['Small', 'Large'],
            'colors' => ['Red', 'Blue'],
        ];

        $response = $this->actingAs($user)->post(route('products.store'), $productData);

        $response->assertRedirect(route('products.index'));
        
        $product = Product::where('sku', 'TSHIRT-001')->first();
        $this->assertNotNull($product);
        $this->assertEquals(['Small', 'Large'], $product->sizes);
        $this->assertEquals(['Red', 'Blue'], $product->colors);
    }

    public function test_user_can_update_product_sizes_and_colors()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['view products', 'edit products']);

        $product = Product::create([
            'name' => 'Old Product',
            'price' => 100,
            'stock' => 10,
            'sizes' => ['Small'],
            'colors' => ['Black'],
        ]);

        $updateData = [
            'name' => 'Updated Product',
            'sku' => 'UPD-001',
            'price' => 120,
            'stock' => 20,
            'sizes' => ['Medium', 'XL'],
            'colors' => ['White', 'Green'],
            'is_active' => '1',
        ];

        $response = $this->actingAs($user)->put(route('products.update', $product), $updateData);

        $response->assertRedirect(route('products.index'));
        
        $product->refresh();
        $this->assertEquals(['Medium', 'XL'], $product->sizes);
        $this->assertEquals(['White', 'Green'], $product->colors);
    }

    public function test_validation_fails_for_invalid_sizes_or_colors()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['view products', 'create products']);

        $productData = [
            'name' => 'Invalid Product',
            'price' => 50,
            'stock' => 10,
            'sizes' => ['InvalidSize'],
            'colors' => ['InvalidColor'],
        ];

        $response = $this->actingAs($user)->post(route('products.store'), $productData);

        $response->assertSessionHasErrors(['sizes.0', 'colors.0']);
    }
}
