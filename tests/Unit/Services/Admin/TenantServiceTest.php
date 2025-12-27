<?php

namespace Tests\Unit\Services\Admin;

use App\DTOs\Admin\TenantInputDTO;
use App\DTOs\Admin\TenantUpdateDTO;
use App\DTOs\SearchDTO;
use App\Models\AdminUser;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Services\Admin\TenantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantServiceTest extends TestCase
{
    use RefreshDatabase;

    private TenantService $tenantService;
    private AdminUser $adminUser;
    private array $createdTenants = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = AdminUser::factory()->create();
        $this->actingAs($this->adminUser, 'admin');

        $this->tenantService = new TenantService();
    }

    protected function tearDown(): void
    {
        // Limpar todos os tenants criados (isso deleta o banco também)
        foreach ($this->createdTenants as $tenant) {
            try {
                if ($tenant->exists) {
                    $tenant->delete();
                }
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

    public function testCreateTenantSuccessfully(): void
    {
        $plan = SubscriptionPlan::factory()->create([
            'name' => 'Basic Plan',
            'price_monthly' => 99.90,
        ]);

        $dto = new TenantInputDTO(
            name: 'Oficina Teste',
            slug: 'oficina-teste-' . rand(1000, 9999), // Slug único para evitar conflito
            subscriptionPlanId: $plan->id,
            domain: 'oficina-teste-' . rand(1000, 9999),
            adminName: 'Admin Teste',
            adminEmail: 'admin@oficina-teste.com',
            adminPassword: 'password123',
            isActive: true
        );

        $result = $this->tenantService->create($dto);
        $this->trackTenant($result);

        $this->assertNotNull($result->id);
        $this->assertEquals('Oficina Teste', $result->name);
        $this->assertTrue(str_starts_with($result->slug, 'oficina-teste-'));
        $this->assertEquals($plan->id, $result->subscription_plan_id);
        $this->assertTrue($result->is_active);

        $this->assertDatabaseHas('tenants', [
            'name' => 'Oficina Teste',
        ]);

        // Domain will have suffix added based on env
        $this->assertDatabaseHas('domains', [
            'tenant_id' => $result->id,
        ]);
    }

    public function testUpdateTenantSuccessfully(): void
    {
        $plan1 = SubscriptionPlan::factory()->create(['name' => 'Plan 1']);
        $plan2 = SubscriptionPlan::factory()->create(['name' => 'Plan 2']);

        $tenant = Tenant::factory()->create([
            'name' => 'Old Name',
            'subscription_plan_id' => $plan1->id,
            'is_active' => true,
        ]);
        $this->trackTenant($tenant);

        $dto = new TenantUpdateDTO(
            name: 'New Name',
            subscriptionPlanId: $plan2->id,
            isActive: false
        );

        $this->tenantService->update($tenant->id, $dto);

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'name' => 'New Name',
            'subscription_plan_id' => $plan2->id,
            'is_active' => false,
        ]);

        $tenant->refresh();
        $this->assertEquals('New Name', $tenant->name);
        $this->assertEquals($plan2->id, $tenant->subscription_plan_id);
        $this->assertFalse($tenant->is_active);
    }

    public function testDeleteTenantSuccessfully(): void
    {
        $tenant = Tenant::factory()->create();
        $this->trackTenant($tenant);

        $this->tenantService->delete($tenant->id);

        $this->assertDatabaseMissing('tenants', [
            'id' => $tenant->id,
        ]);
    }

    public function testListTenantsWithPagination(): void
    {
        $tenants = Tenant::factory()->count(15)->create();
        foreach ($tenants as $tenant) {
            $this->trackTenant($tenant);
        }

        $searchDTO = SearchDTO::fromRequest(new \Illuminate\Http\Request([
            'page' => 1,
            'per_page' => 10,
        ]));

        $result = $this->tenantService->list($searchDTO);

        $this->assertCount(10, $result->items());
        $this->assertEquals(15, $result->total());
    }

    public function testListTenantsWithSearch(): void
    {
        $tenant1 = Tenant::factory()->create(['name' => 'Oficina ABC']);
        $tenant2 = Tenant::factory()->create(['name' => 'Oficina XYZ']);
        $tenant3 = Tenant::factory()->create(['name' => 'Mecânica 123']);

        $this->trackTenant($tenant1);
        $this->trackTenant($tenant2);
        $this->trackTenant($tenant3);

        $searchDTO = SearchDTO::fromRequest(new \Illuminate\Http\Request([
            'search' => 'Oficina',
            'page' => 1,
            'per_page' => 10,
        ]));

        $result = $this->tenantService->list($searchDTO);

        $this->assertCount(2, $result->items());
        $this->assertEquals(2, $result->total());
    }

    public function testGetActiveSubscriptionPlans(): void
    {
        SubscriptionPlan::factory()->create(['is_active' => true, 'name' => 'Active 1']);
        SubscriptionPlan::factory()->create(['is_active' => true, 'name' => 'Active 2']);
        SubscriptionPlan::factory()->create(['is_active' => false, 'name' => 'Inactive']);

        $result = $this->tenantService->getActiveSubscriptionPlans();

        $this->assertCount(2, $result);
    }

    public function testCreateTenantWithInactiveStatus(): void
    {
        $plan = SubscriptionPlan::factory()->create();

        $dto = new TenantInputDTO(
            name: 'Inactive Tenant',
            slug: 'inactive-tenant-' . rand(1000, 9999),
            subscriptionPlanId: $plan->id,
            domain: 'inactive-' . rand(1000, 9999),
            adminName: 'Admin',
            adminEmail: 'admin@inactive.test',
            adminPassword: 'password',
            isActive: false
        );

        $result = $this->tenantService->create($dto);
        $this->trackTenant($result);

        $this->assertFalse($result->is_active);
        $this->assertDatabaseHas('tenants', [
            'id' => $result->id,
            'is_active' => false,
        ]);
    }
}
