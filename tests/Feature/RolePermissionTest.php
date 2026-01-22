<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Lead;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_delete_lead()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $lead = Lead::factory()->create();

        $response = $this->actingAs($admin)->delete(route('leads.destroy', $lead));

        $response->assertRedirect(route('leads.index'));
        $this->assertSoftDeleted('leads', ['id' => $lead->id]);
    }

    public function test_staff_cannot_delete_lead()
    {
        $staff = User::factory()->create();
        $staff->assignRole('agent'); // 'staff' role doesn't exist in seeder, 'agent' does
        // But agent DOES have delete leads permission? Let's check seeder.
        // Seeder says Agent has: view dashboard, view leads, create leads, edit leads, view orders, create orders, view products, view campaigns.
        // Agent does NOT have delete leads. So this test should pass (Forbidden).
        $lead = Lead::factory()->create();

        $response = $this->actingAs($staff)->delete(route('leads.destroy', $lead));

        $response->assertForbidden();
        $this->assertNotSoftDeleted('leads', ['id' => $lead->id]);
    }

    public function test_admin_can_delete_order()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $lead = Lead::factory()->create();
        $order = Order::factory()->create(['lead_id' => $lead->id]);

        $response = $this->actingAs($admin)->delete(route('orders.destroy', $order));

        $response->assertRedirect(route('leads.show', $lead));
        $this->assertSoftDeleted('orders', ['id' => $order->id]);
    }

    public function test_staff_cannot_delete_order()
    {
        $staff = User::factory()->create();
        $staff->assignRole('agent');
        $lead = Lead::factory()->create();
        $order = Order::factory()->create(['lead_id' => $lead->id]);

        $response = $this->actingAs($staff)->delete(route('orders.destroy', $order));

        $response->assertForbidden();
        $this->assertNotSoftDeleted('orders', ['id' => $order->id]);
    }

    public function test_admin_can_delete_campaign()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $campaign = Campaign::factory()->create();

        $response = $this->actingAs($admin)->delete(route('campaigns.destroy', $campaign));

        $response->assertRedirect(route('campaigns.index'));
        $this->assertSoftDeleted('campaigns', ['id' => $campaign->id]);
    }

    public function test_staff_cannot_delete_campaign()
    {
        $staff = User::factory()->create();
        $staff->assignRole('agent');
        $campaign = Campaign::factory()->create();

        $response = $this->actingAs($staff)->delete(route('campaigns.destroy', $campaign));

        $response->assertForbidden();
        $this->assertNotSoftDeleted('campaigns', ['id' => $campaign->id]);
    }

    public function test_delete_button_visibility()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $staff = User::factory()->create();
        $staff->assignRole('agent');
        
        $lead = Lead::factory()->create();

        // Admin should see delete button (checking for the form action url or button text)
        $response = $this->actingAs($admin)->get(route('leads.index'));
        $response->assertSee('value="DELETE"', false); // Check for method spoofing
        $response->assertSee('حذف');

        // Staff should NOT see delete button
        $response = $this->actingAs($staff)->get(route('leads.index'));
        $response->assertDontSee('value="DELETE"', false);
        $response->assertDontSee('حذف');
    }

    public function test_order_delete_button_visibility_in_lead_show()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $staff = User::factory()->create();
        $staff->assignRole('agent');
        
        $lead = Lead::factory()->create();
        $order = Order::factory()->create(['lead_id' => $lead->id]);

        // Admin should see delete button for order
        $response = $this->actingAs($admin)->get(route('leads.show', $lead));
        $response->assertSee('value="DELETE"', false);
        $response->assertSee('حذف');

        // Staff should NOT see delete button for order
        $response = $this->actingAs($staff)->get(route('leads.show', $lead));
        $response->assertDontSee('value="DELETE"', false);
        $response->assertDontSee('حذف');
    }

    public function test_admin_can_access_user_management()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertOk();
    }

    public function test_staff_cannot_access_user_management()
    {
        $staff = User::factory()->create();
        $staff->assignRole('agent');

        $response = $this->actingAs($staff)->get(route('users.index'));

        $response->assertForbidden();
    }
}
