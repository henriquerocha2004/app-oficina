<?php

namespace Tests\Unit\Services\Admin;

use App\DTOs\Admin\SubscriptionPlanInputDTO;
use App\DTOs\SearchDTO;
use App\Models\AdminUser;
use App\Models\SubscriptionPlan;
use App\Services\Admin\SubscriptionPlanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionPlanServiceTest extends TestCase
{
    use RefreshDatabase;

    private SubscriptionPlanService $planService;
    private AdminUser $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = AdminUser::factory()->create();
        $this->actingAs($this->adminUser, 'admin');

        $this->planService = new SubscriptionPlanService();
    }

    public function testCreatePlanSuccessfully(): void
    {
        $dto = new SubscriptionPlanInputDTO(
            name: 'Premium Plan',
            slug: 'premium-plan',
            description: 'Premium features',
            price: 199.90,
            billingCycle: 'monthly',
            maxUsers: 10,
            maxClients: 1000,
            maxVehicles: 5000,
            maxServicesPerMonth: 500,
            features: ['Feature 1', 'Feature 2'],
            isActive: true
        );

        $result = $this->planService->create($dto);

        $this->assertNotNull($result->id);
        $this->assertEquals('Premium Plan', $result->name);
        $this->assertEquals('premium-plan', $result->slug);
        $this->assertEquals(199.90, $result->price_monthly);
        $this->assertEquals(1999.00, $result->price_yearly);
        $this->assertEquals(10, $result->limits['max_users']);
        $this->assertTrue($result->is_active);

        $this->assertDatabaseHas('subscription_plans', [
            'name' => 'Premium Plan',
            'slug' => 'premium-plan',
        ]);
    }

    public function testUpdatePlanSuccessfully(): void
    {
        $plan = SubscriptionPlan::factory()->create([
            'name' => 'Old Plan',
            'price_monthly' => 99.90,
            'is_active' => true,
        ]);

        $dto = new SubscriptionPlanInputDTO(
            name: 'Updated Plan',
            slug: 'updated-plan',
            description: 'Updated description',
            price: 149.90,
            billingCycle: 'yearly',
            maxUsers: 5,
            maxClients: 500,
            maxVehicles: 2500,
            maxServicesPerMonth: 250,
            features: ['Updated Feature'],
            isActive: false
        );

        $this->planService->update($plan->id, $dto);

        $this->assertDatabaseHas('subscription_plans', [
            'id' => $plan->id,
            'name' => 'Updated Plan',
            'price_yearly' => 149.90,
            'is_active' => false,
        ]);

        $plan->refresh();
        $this->assertEquals('Updated Plan', $plan->name);
        $this->assertFalse($plan->is_active);
    }

    public function testDeletePlanSuccessfully(): void
    {
        $plan = SubscriptionPlan::factory()->create();

        $this->planService->delete($plan->id);

        $this->assertDatabaseMissing('subscription_plans', [
            'id' => $plan->id,
        ]);
    }

    public function testListPlansWithPagination(): void
    {
        SubscriptionPlan::factory()->count(15)->create();

        $searchDTO = SearchDTO::fromRequest(new \Illuminate\Http\Request([
            'page' => 1,
            'per_page' => 10,
        ]));

        $result = $this->planService->list($searchDTO);

        $this->assertCount(10, $result->items());
        $this->assertEquals(15, $result->total());
    }

    public function testListPlansWithSearch(): void
    {
        SubscriptionPlan::factory()->create(['name' => 'Basic Plan']);
        SubscriptionPlan::factory()->create(['name' => 'Premium Plan']);
        SubscriptionPlan::factory()->create(['name' => 'Enterprise Package']);

        $searchDTO = SearchDTO::fromRequest(new \Illuminate\Http\Request([
            'search' => 'Plan',
            'page' => 1,
            'per_page' => 10,
        ]));

        $result = $this->planService->list($searchDTO);

        $this->assertCount(2, $result->items());
        $this->assertEquals(2, $result->total());
    }

    public function testCreatePlanWithInactiveStatus(): void
    {
        $dto = new SubscriptionPlanInputDTO(
            name: 'Inactive Plan',
            slug: 'inactive-plan',
            description: 'This plan is inactive',
            price: 99.90,
            billingCycle: 'monthly',
            maxUsers: 1,
            maxClients: 100,
            maxVehicles: 500,
            maxServicesPerMonth: 50,
            features: ['Basic'],
            isActive: false
        );

        $result = $this->planService->create($dto);

        $this->assertFalse($result->is_active);
        $this->assertDatabaseHas('subscription_plans', [
            'id' => $result->id,
            'is_active' => false,
        ]);
    }

    public function testCreatePlanWithFeaturesArray(): void
    {
        $features = [
            'Feature A',
            'Feature B',
            'Feature C',
        ];

        $dto = new SubscriptionPlanInputDTO(
            name: 'Feature Test Plan',
            slug: 'feature-test',
            description: 'Testing features',
            price: 99.90,
            billingCycle: 'monthly',
            maxUsers: 1,
            maxClients: 100,
            maxVehicles: 500,
            maxServicesPerMonth: 50,
            features: $features,
            isActive: true
        );

        $result = $this->planService->create($dto);

        $this->assertIsArray($result->features);
        $this->assertCount(3, $result->features);
        $this->assertContains('Feature A', $result->features);
    }
}
