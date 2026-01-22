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
}
