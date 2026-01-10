<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AdminPlansControllerTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = AdminUser::factory()->create();
        $this->actingAs($this->adminUser, 'admin');
    }

    public function testIndexReturnsInertiaResponse(): void
    {
        $response = $this->get('/admin/plans');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertInertia(fn($page) => $page->component('admin/plans/Index'));
    }

    public function testIndexReturnsPlansData(): void
    {
        SubscriptionPlan::factory()->count(3)->create();

        $response = $this->get('/admin/plans');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertInertia(fn($page) => $page
            ->component('admin/plans/Index')
            ->has('plans'));
    }

    public function testStoreCreatesPlanSuccessfully(): void
    {
        $planData = [
            'name' => 'Premium Plan',
            'slug' => 'premium-plan',
            'description' => 'Premium features',
            'price' => 199.90,
            'billing_cycle' => 'monthly',
            'max_users' => 10,
            'max_clients' => 1000,
            'max_vehicles' => 5000,
            'max_services_per_month' => 500,
            'features' => 'Feature 1, Feature 2, Feature 3',
            'is_active' => true,
        ];

        $response = $this->post('/admin/plans', $planData);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subscription_plans', [
            'name' => 'Premium Plan',
            'slug' => 'premium-plan',
            'price_monthly' => 199.90,
        ]);
    }

    public function testStoreValidatesRequiredFields(): void
    {
        $response = $this->post('/admin/plans', []);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasErrors([
            'name',
            'slug',
            'price',
            'billing_cycle',
            'max_users',
            'max_clients',
            'max_vehicles',
            'max_services_per_month',
        ]);
    }

    public function testStoreValidatesUniqueSlug(): void
    {
        SubscriptionPlan::factory()->create(['slug' => 'existing-slug']);

        $planData = [
            'name' => 'New Plan',
            'slug' => 'existing-slug',
            'description' => 'Description',
            'price' => 99.90,
            'billing_cycle' => 'monthly',
            'max_users' => 1,
            'max_clients' => 100,
            'max_vehicles' => 500,
            'max_services_per_month' => 50,
            'features' => 'Feature',
            'is_active' => true,
        ];

        $response = $this->post('/admin/plans', $planData);

        $response->assertSessionHasErrors(['slug']);
    }

    public function testStoreValidatesBillingCycle(): void
    {
        $planData = [
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'price' => 99.90,
            'billing_cycle' => 'invalid',
            'max_users' => 1,
            'max_clients' => 100,
            'max_vehicles' => 500,
            'max_services_per_month' => 50,
        ];

        $response = $this->post('/admin/plans', $planData);

        $response->assertSessionHasErrors(['billing_cycle']);
    }

    public function testUpdateModifiesPlanSuccessfully(): void
    {
        $plan = SubscriptionPlan::factory()->create([
            'name' => 'Old Plan',
            'price_monthly' => 99.90,
            'is_active' => true,
        ]);

        $updateData = [
            'name' => 'Updated Plan',
            'slug' => 'updated-plan',
            'description' => 'Updated',
            'price' => 149.90,
            'billing_cycle' => 'yearly',
            'max_users' => 5,
            'max_clients' => 500,
            'max_vehicles' => 2500,
            'max_services_per_month' => 250,
            'features' => 'Updated Feature',
            'is_active' => false,
        ];

        $response = $this->put("/admin/plans/{$plan->id}", $updateData);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subscription_plans', [
            'id' => $plan->id,
            'name' => 'Updated Plan',
            'price_yearly' => 149.90,
            'is_active' => false,
        ]);
    }

    public function testUpdateValidatesRequiredFields(): void
    {
        $plan = SubscriptionPlan::factory()->create();

        $response = $this->put("/admin/plans/{$plan->id}", []);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasErrors(['name', 'slug', 'price']);
    }

    public function testDestroyDeletesPlanSuccessfully(): void
    {
        $plan = SubscriptionPlan::factory()->create();

        $response = $this->delete("/admin/plans/{$plan->id}");

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('subscription_plans', [
            'id' => $plan->id,
        ]);
    }

    public function testDestroyReturnsNotFoundForNonExistentPlan(): void
    {
        $response = $this->delete('/admin/plans/non-existent-id');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testListReturnsJsonWithPaginatedData(): void
    {
        SubscriptionPlan::factory()->count(15)->create();

        $response = $this->getJson('/admin/plans/filters?page=1&per_page=10');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'plans' => [
                'items' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                    ],
                ],
                'total_items',
            ],
        ]);

        $this->assertCount(10, $response->json('plans.items'));
        $this->assertEquals(15, $response->json('plans.total_items'));
    }

    public function testListAppliesSearchFilter(): void
    {
        SubscriptionPlan::factory()->create(['name' => 'Basic Plan']);
        SubscriptionPlan::factory()->create(['name' => 'Premium Plan']);
        SubscriptionPlan::factory()->create(['name' => 'Enterprise Package']);

        $response = $this->getJson('/admin/plans/filters?search=Plan');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(2, $response->json('plans.items'));
    }

    public function testRequiresAdminAuthentication(): void
    {
        auth()->guard('admin')->logout();

        $response = $this->get('/admin/plans');

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect('/login');
    }

    public function testCreatePlanWithInactiveStatus(): void
    {
        $planData = [
            'name' => 'Inactive Plan',
            'slug' => 'inactive-plan',
            'description' => 'Inactive',
            'price' => 99.90,
            'billing_cycle' => 'monthly',
            'max_users' => 1,
            'max_clients' => 100,
            'max_vehicles' => 500,
            'max_services_per_month' => 50,
            'features' => 'Basic',
            'is_active' => false,
        ];

        $response = $this->post('/admin/plans', $planData);

        $response->assertStatus(Response::HTTP_FOUND);

        $this->assertDatabaseHas('subscription_plans', [
            'name' => 'Inactive Plan',
            'is_active' => false,
        ]);
    }
}
