<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_staff_can_download_invoice()
    {
        $staff = User::factory()->create();
        $staff->assignRole('agent');
        $order = Order::factory()->create();

        $response = $this->actingAs($staff)->get(route('orders.invoice', $order));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_download_invoice()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $order = Order::factory()->create();

        $response = $this->actingAs($admin)->get(route('orders.invoice', $order));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_guest_cannot_download_invoice()
    {
        $order = Order::factory()->create();

        $response = $this->get(route('orders.invoice', $order));

        $response->assertRedirect(route('login'));
    }
}
