<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_roles_list()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('roles.index'));

        $response->assertStatus(200);
        $response->assertSee('إدارة الأدوار');
    }

    public function test_non_admin_cannot_view_roles_list()
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $response = $this->actingAs($manager)->get(route('roles.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_create_new_role()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('roles.store'), [
            'name' => 'super_agent',
            'permissions' => ['view leads', 'create leads']
        ]);

        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'super_agent']);
        
        $role = Role::findByName('super_agent');
        $this->assertTrue($role->hasPermissionTo('view leads'));
    }

    public function test_admin_can_update_role()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $role = Role::create(['name' => 'test_role']);
        $role->givePermissionTo('view leads');

        $response = $this->actingAs($admin)->put(route('roles.update', $role), [
            'name' => 'updated_role',
            'permissions' => ['view orders']
        ]);

        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'updated_role']);
        
        $role->refresh();
        $this->assertFalse($role->hasPermissionTo('view leads'));
        $this->assertTrue($role->hasPermissionTo('view orders'));
    }

    public function test_admin_cannot_delete_admin_role()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $adminRole = Role::findByName('admin');

        $response = $this->actingAs($admin)->delete(route('roles.destroy', $adminRole));

        $this->assertDatabaseHas('roles', ['name' => 'admin']);
    }

    public function test_admin_can_delete_other_roles()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $role = Role::create(['name' => 'temp_role']);

        $response = $this->actingAs($admin)->delete(route('roles.destroy', $role));

        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseMissing('roles', ['name' => 'temp_role']);
    }
}
