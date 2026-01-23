<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Run the seeder to setup roles and permissions
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_access_users_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertStatus(200);
    }

    public function test_agent_cannot_access_users_page()
    {
        $agent = User::factory()->create();
        $agent->assignRole('agent');

        $response = $this->actingAs($agent)->get(route('users.index'));

        $response->assertStatus(403);
    }

    public function test_manager_can_access_reports()
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $response = $this->actingAs($manager)->get(route('reports.leads'));

        $response->assertStatus(200);
    }

    public function test_agent_cannot_access_reports()
    {
        $agent = User::factory()->create();
        $agent->assignRole('agent');

        $response = $this->actingAs($agent)->get(route('reports.leads'));

        $response->assertStatus(403);
    }

    public function test_agent_can_access_products()
    {
        $agent = User::factory()->create();
        $agent->assignRole('agent');

        $response = $this->actingAs($agent)->get(route('products.index'));

        $response->assertStatus(200);
    }

    public function test_agent_cannot_delete_leads()
    {
        $agent = User::factory()->create();
        $agent->assignRole('agent');
        $lead = \App\Models\Lead::factory()->create();

        $response = $this->actingAs($agent)->delete(route('leads.destroy', $lead));

        $response->assertStatus(403);
    }

    public function test_agent_cannot_delete_orders()
    {
        $agent = User::factory()->create();
        $agent->assignRole('agent');
        $order = \App\Models\Order::factory()->create();

        $response = $this->actingAs($agent)->delete(route('orders.destroy', $order));

        $response->assertStatus(403);
    }

    public function test_agent_cannot_create_products()
    {
        $agent = User::factory()->create();
        $agent->assignRole('agent');

        $response = $this->actingAs($agent)->get(route('products.create'));
        $response->assertStatus(403);

        $response = $this->actingAs($agent)->post(route('products.store'), [
            'name' => 'New Product',
            'price' => 100,
            'stock' => 10
        ]);
        $response->assertStatus(403);
    }

    public function test_custom_role_permissions()
    {
        // Create a custom role with ONLY 'view leads' permission
        $customRole = Role::create(['name' => 'custom_viewer']);
        $customRole->givePermissionTo('view leads');

        $user = User::factory()->create();
        $user->assignRole('custom_viewer');

        // Should be able to view leads
        $this->actingAs($user)->get(route('leads.index'))->assertStatus(200);

        // Should NOT be able to create leads
        $this->actingAs($user)->get(route('leads.create'))->assertStatus(403);
        
        // Should NOT be able to view orders
        $this->actingAs($user)->get(route('orders.index'))->assertStatus(403);
    }
}
