<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Campaign;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Create permissions
        $permissions = [
            'view dashboard', 'view leads', 'create leads', 'edit leads', 'delete leads',
            'view orders', 'create orders', 'edit orders', 'delete orders',
            'view products', 'create products', 'edit products', 'delete products',
            'view campaigns', 'create campaigns', 'edit campaigns', 'delete campaigns',
            'view reports', 'manage users', 'view permissions'
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());
        
        Role::create(['name' => 'agent']);
        Role::create(['name' => 'manager']);
    }

    public function test_lead_creation_logs_activity()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $campaign = Campaign::factory()->create();

        $this->post(route('leads.store'), [
            'customer_name' => 'Test Lead',
            'phone' => '1234567890',
            'status' => 'New',
            'campaign_id' => $campaign->id,
        ]);

        $lead = Lead::first();
        $this->assertDatabaseHas('activity_logs', [
            'description' => 'تم إنشاء عميل جديد',
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'causer_id' => $admin->id,
        ]);
    }

    public function test_lead_update_logs_activity()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $lead = Lead::factory()->create();

        $this->put(route('leads.update', $lead), [
            'customer_name' => 'Updated Name',
            'phone' => $lead->phone,
            'status' => $lead->status,
            'campaign_id' => $lead->campaign_id,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'description' => 'تم تحديث بيانات العميل',
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
        ]);
    }

    public function test_order_creation_logs_activity()
    {
        $this->withoutExceptionHandling();
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $lead = Lead::factory()->create();
        $product = Product::factory()->create();

        $this->post(route('orders.store'), [
            'lead_id' => $lead->id,
            'order_status' => 'Pending',
            'payment_method' => 'Cash',
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => 1,
                    'unit_price' => 100,
                    'variant' => 'Default',
                ]
            ],
        ]);

        $order = Order::first();
        $this->assertDatabaseHas('activity_logs', [
            'description' => 'تم إنشاء طلب جديد',
            'subject_type' => Order::class,
            'subject_id' => $order->id,
        ]);
    }

    public function test_campaign_creation_logs_activity()
    {
        $this->withoutExceptionHandling();
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $this->post(route('campaigns.store'), [
            'name' => 'New Campaign',
            'platform' => 'Facebook',
            'ad_type' => 'Image',
            'source' => 'Website',
        ]);

        $campaign = Campaign::first();
        $this->assertDatabaseHas('activity_logs', [
            'description' => 'تم إنشاء حملة جديدة',
            'subject_type' => Campaign::class,
            'subject_id' => $campaign->id,
        ]);
    }

    public function test_activity_log_visible_on_lead_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $lead = Lead::factory()->create();
        
        // Create activity manually
        \App\Services\ActivityLogger::log('Test Activity', $lead);

        $response = $this->get(route('leads.show', $lead));
        $response->assertSee('Test Activity');
        $response->assertSee('سجل النشاط');
    }
}
