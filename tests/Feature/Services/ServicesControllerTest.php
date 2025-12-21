<?php

namespace Tests\Feature\Services;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Helpers\TenantTestHelper;

class ServicesControllerTest extends TestCase
{
    use RefreshDatabase;
    use TenantTestHelper;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize tenant for testing
        $this->initializeTenant();

        // Create and authenticate a test user within tenant context
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function testStoreCreatesServiceSuccessfully(): void
    {
        $serviceData = [
            'name' => 'Troca de Óleo e Filtro',
            'description' => 'Troca completa de óleo do motor e filtro de óleo',
            'base_price' => 150.00,
            'category' => Service::CATEGORY_MAINTENANCE,
            'estimated_time' => 60,
            'is_active' => true,
        ];

        $response = $this->postJson('/services', $serviceData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Service created successfully',
            ])
            ->assertJsonStructure([
                'service_id',
            ]);

        $this->assertDatabaseHas('services', [
            'name' => 'Troca de Óleo e Filtro',
            'base_price' => 150.00,
            'category' => Service::CATEGORY_MAINTENANCE,
        ]);
    }

    public function testStoreValidatesRequiredFields(): void
    {
        $response = $this->postJson('/services', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name', 'base_price', 'category']);
    }

    public function testStoreValidatesCategoryValues(): void
    {
        $serviceData = [
            'name' => 'Test Service',
            'base_price' => 100.00,
            'category' => 'invalid_category',
        ];

        $response = $this->postJson('/services', $serviceData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['category']);
    }

    public function testStoreValidatesPriceRange(): void
    {
        $serviceData = [
            'name' => 'Test Service',
            'base_price' => -10.00,
            'category' => Service::CATEGORY_MAINTENANCE,
        ];

        $response = $this->postJson('/services', $serviceData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['base_price']);
    }

    public function testShowByIdReturnsServiceSuccessfully(): void
    {
        $service = Service::factory()->create([
            'name' => 'Alinhamento',
            'base_price' => 120.00,
            'category' => Service::CATEGORY_ALIGNMENT,
        ]);

        $response = $this->getJson("/services/{$service->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'service' => [
                    'id',
                    'name',
                    'description',
                    'base_price',
                    'category',
                    'estimated_time',
                    'is_active',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'service' => [
                    'name' => 'Alinhamento',
                    'base_price' => 120.00,
                    'category' => Service::CATEGORY_ALIGNMENT,
                ],
            ]);
    }

    public function testShowByIdReturnsNotFoundForNonExistentService(): void
    {
        $response = $this->getJson('/services/non-existent-id');

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Service not found.',
            ]);
    }

    public function testUpdateServiceSuccessfully(): void
    {
        $service = Service::factory()->create([
            'name' => 'Original Name',
            'base_price' => 100.00,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'base_price' => 200.00,
            'category' => Service::CATEGORY_REPAIR,
        ];

        $response = $this->putJson("/services/{$service->id}", $updateData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Service updated successfully',
            ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Updated Name',
            'base_price' => 200.00,
            'category' => Service::CATEGORY_REPAIR,
        ]);
    }

    public function testUpdateReturnsNotFoundForNonExistentService(): void
    {
        $updateData = [
            'name' => 'Updated Name',
            'base_price' => 200.00,
            'category' => Service::CATEGORY_REPAIR,
        ];

        $response = $this->putJson('/services/non-existent-id', $updateData);

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Service not found.',
            ]);
    }

    public function testDeleteServiceSuccessfully(): void
    {
        $service = Service::factory()->create();

        $response = $this->deleteJson("/services/{$service->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Service deleted successfully',
            ]);

        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }

    public function testDeleteReturnsNotFoundForNonExistentService(): void
    {
        $response = $this->deleteJson('/services/non-existent-id');

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Service not found.',
            ]);
    }

    public function testFindByFiltersReturnsServicesSuccessfully(): void
    {
        Service::factory()->count(3)->create();

        $response = $this->getJson('/services/filters');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'services' => [
                    'items',
                    'total_items',
                ],
            ]);
    }

    public function testFindByFiltersWithSearchParameter(): void
    {
        Service::factory()->create(['name' => 'Troca de Óleo']);
        Service::factory()->create(['name' => 'Alinhamento']);
        Service::factory()->create(['name' => 'Pintura']);

        $response = $this->getJson('/services/filters?search=Óleo');

        $response->assertStatus(Response::HTTP_OK);

        $services = $response->json('services.items');
        $this->assertCount(1, $services);
        $this->assertStringContainsString('Óleo', $services[0]['name']);
    }

    public function testFindByFiltersWithCategoryFilter(): void
    {
        Service::factory()->create(['category' => Service::CATEGORY_MAINTENANCE]);
        Service::factory()->create(['category' => Service::CATEGORY_REPAIR]);
        Service::factory()->create(['category' => Service::CATEGORY_MAINTENANCE]);

        $response = $this->getJson(
            '/services/filters?category=' . Service::CATEGORY_MAINTENANCE
        );

        $response->assertStatus(Response::HTTP_OK);
        $this->assertGreaterThanOrEqual(2, $response->json('services.total_items'));
    }

    public function testFindByFiltersWithPriceRangeFilter(): void
    {
        Service::factory()->create(['base_price' => 50.00]);
        Service::factory()->create(['base_price' => 150.00]);
        Service::factory()->create(['base_price' => 300.00]);

        $response = $this->getJson('/services/filters?min_price=100&max_price=200');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertGreaterThanOrEqual(1, $response->json('services.total_items'));
    }

    public function testFindByCategoryReturnsServicesSuccessfully(): void
    {
        Service::factory()->count(2)->create([
            'category' => Service::CATEGORY_MAINTENANCE,
            'is_active' => true,
        ]);
        Service::factory()->create([
            'category' => Service::CATEGORY_REPAIR,
            'is_active' => true,
        ]);

        $response = $this->getJson('/services/category/' . Service::CATEGORY_MAINTENANCE);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'services',
            ]);

        $services = $response->json('services');
        $this->assertCount(2, $services);
        foreach ($services as $service) {
            $this->assertEquals(Service::CATEGORY_MAINTENANCE, $service['category']);
        }
    }

    public function testToggleActiveChangesServiceStatus(): void
    {
        $service = Service::factory()->create(['is_active' => true]);

        $response = $this->patchJson("/services/{$service->id}/toggle-active");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Service status updated successfully',
            ])
            ->assertJsonPath('service.is_active', false);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'is_active' => false,
        ]);
    }

    public function testToggleActiveReturnsNotFoundForNonExistentService(): void
    {
        $response = $this->patchJson('/services/non-existent-id/toggle-active');

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Service not found.',
            ]);
    }

    public function testListActiveReturnsOnlyActiveServices(): void
    {
        Service::factory()->count(3)->create(['is_active' => true]);
        Service::factory()->count(2)->create(['is_active' => false]);

        $response = $this->getJson('/services/active');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(3, $response->json('services.total_items'));

        $services = $response->json('services.items');
        foreach ($services as $service) {
            $this->assertTrue($service['is_active']);
        }
    }

    public function testListServicesWithSorting(): void
    {
        Service::factory()->create(['name' => 'Zebra Service', 'base_price' => 100.00]);
        Service::factory()->create(['name' => 'Alpha Service', 'base_price' => 300.00]);
        Service::factory()->create(['name' => 'Beta Service', 'base_price' => 200.00]);

        $response = $this->getJson('/services/filters?sort_by=name&sort_direction=asc');

        $response->assertStatus(Response::HTTP_OK);
        $services = $response->json('services.items');
        $this->assertEquals('Alpha Service', $services[0]['name']);
    }

    public function testListServicesWithPagination(): void
    {
        Service::factory()->count(15)->create();

        $response = $this->getJson('/services/filters?per_page=5');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(5, $response->json('services.items'));
        $this->assertEquals(15, $response->json('services.total_items'));
    }
}
