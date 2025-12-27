<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AdminTenantsControllerTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $adminUser;
    private array $createdTenants = [];
    private array $existingTenantIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = AdminUser::factory()->create();
        $this->actingAs($this->adminUser, 'admin');

        // Track existing tenants before test
        $this->existingTenantIds = Tenant::pluck('id')->toArray();
    }

    protected function tearDown(): void
    {
        // Cleanup manually tracked tenants
        foreach ($this->createdTenants as $tenant) {
            try {
                if ($tenant->exists) {
                    $tenant->delete();
                }
            } catch (\Exception $e) {
                // Ignorar erros de cleanup
            }
        }

        // Cleanup any tenants created during the test (e.g., via HTTP requests)
        $newTenants = Tenant::whereNotIn('id', $this->existingTenantIds)->get();
        foreach ($newTenants as $tenant) {
            try {
                $tenant->delete();
            } catch (\Exception $e) {
                // Ignorar erros de cleanup
            }
        }

        parent::tearDown();
    }

    private function trackTenant(Tenant $tenant): Tenant
    {
        $this->createdTenants[] = $tenant;
        return $tenant;
    }

    public function testIndexReturnsInertiaResponse(): void
    {
        $response = $this->get('/admin/tenants');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertInertia(fn($page) => $page->component('admin/tenants/Index'));
    }

    public function testIndexReturnsTenantsData(): void
    {
        $tenants = Tenant::factory()->count(3)->create();
        foreach ($tenants as $tenant) {
            $this->trackTenant($tenant);
        }

        $response = $this->get('/admin/tenants');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertInertia(fn($page) => $page
            ->component('admin/tenants/Index')
            ->has('tenants'));
    }

    public function testStoreCreatesTenantSuccessfully(): void
    {
        $plan = SubscriptionPlan::factory()->create();

        $tenantData = [
            'name' => 'Nova Oficina',
            'slug' => 'nova-oficina',
            'subscription_plan_id' => $plan->id,
            'domain' => 'nova_oficina',
            'admin_name' => 'Admin Teste',
            'admin_email' => 'admin@nova-oficina.com',
            'admin_password' => 'Xk9#mP2$vL5@qR8!',
            'is_active' => true,
        ];

        $response = $this->post('/admin/tenants', $tenantData);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tenants', [
            'name' => 'Nova Oficina',
            'slug' => 'nova-oficina',
        ]);

        $this->assertDatabaseHas('domains', [
            'domain' => 'nova_oficina.',
        ]);
    }

    public function testStoreValidatesRequiredFields(): void
    {
        $response = $this->post('/admin/tenants', []);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasErrors([
            'name',
            'slug',
            'subscription_plan_id',
            'domain',
            'admin_name',
            'admin_email',
            'admin_password',
        ]);
    }

    public function testStoreValidatesUniqueSlug(): void
    {
        $plan = SubscriptionPlan::factory()->create();
        $this->trackTenant(Tenant::factory()->create(['slug' => 'existing-slug']));

        $tenantData = [
            'name' => 'New Tenant',
            'slug' => 'existing-slug',
            'subscription_plan_id' => $plan->id,
            'domain' => 'new.test',
            'admin_name' => 'Admin',
            'admin_email' => 'admin@new.test',
            'admin_password' => 'password',
            'is_active' => true,
        ];

        $response = $this->post('/admin/tenants', $tenantData);

        $response->assertSessionHasErrors(['slug']);
    }

    public function testUpdateModifiesTenantSuccessfully(): void
    {
        $plan1 = SubscriptionPlan::factory()->create();
        $plan2 = SubscriptionPlan::factory()->create();

        $tenant = $this->trackTenant(Tenant::factory()->create([
            'name' => 'Old Name',
            'subscription_plan_id' => $plan1->id,
            'is_active' => true,
        ]));

        $updateData = [
            'name' => 'Updated Name',
            'subscription_plan_id' => $plan2->id,
            'is_active' => false,
        ];

        $response = $this->put("/admin/tenants/{$tenant->id}", $updateData);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'name' => 'Updated Name',
            'subscription_plan_id' => $plan2->id,
            'is_active' => false,
        ]);
    }

    public function testUpdateValidatesRequiredFields(): void
    {
        $tenant = $this->trackTenant(Tenant::factory()->create());

        $response = $this->put("/admin/tenants/{$tenant->id}", []);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasErrors(['name', 'subscription_plan_id']);
    }

    public function testDestroyDeletesTenantSuccessfully(): void
    {
        $tenant = $this->trackTenant(Tenant::factory()->create());

        $response = $this->delete("/admin/tenants/{$tenant->id}");

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tenants', [
            'id' => $tenant->id,
        ]);
    }

    public function testDestroyReturnsNotFoundForNonExistentTenant(): void
    {
        $response = $this->delete('/admin/tenants/non-existent-id');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testListReturnsJsonWithPaginatedData(): void
    {
        $tenants = Tenant::factory()->count(15)->create();
        foreach ($tenants as $tenant) {
            $this->trackTenant($tenant);
        }

        $response = $this->getJson('/admin/tenants/filters?page=1&per_page=10');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'tenants' => [
                'items' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'is_active',
                        'subscription_plan',
                        'created_at',
                    ],
                ],
                'total_items',
            ],
        ]);

        $this->assertCount(10, $response->json('tenants.items'));
        $this->assertEquals(15, $response->json('tenants.total_items'));
    }

    public function testListAppliesSearchFilter(): void
    {
        $this->trackTenant(Tenant::factory()->create(['name' => 'Oficina ABC']));
        $this->trackTenant(Tenant::factory()->create(['name' => 'Oficina XYZ']));
        $this->trackTenant(Tenant::factory()->create(['name' => 'Mecânica 123']));

        $response = $this->getJson('/admin/tenants/filters?search=Oficina');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(2, $response->json('tenants.items'));
    }

    public function testRequiresAdminAuthentication(): void
    {
        auth()->guard('admin')->logout();

        $response = $this->get('/admin/tenants');

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect('/login');
    }
}
