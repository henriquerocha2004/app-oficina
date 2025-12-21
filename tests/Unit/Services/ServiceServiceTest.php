<?php

namespace Tests\Unit\Services;

use App\DTOs\ServiceInputDTO;
use App\DTOs\SearchDTO;
use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\Helpers\TenantTestHelper;
use Tests\TestCase;

class ServiceServiceTest extends TestCase
{
    use RefreshDatabase;
    use TenantTestHelper;

    private ServiceService $serviceService;

    protected function setUp(): void
    {
        parent::setUp();
        // Initialize tenant for Unit tests
        $this->initializeTenant();
        $this->serviceService = new ServiceService();
    }

    public function testCreateServiceSuccessfully(): void
    {
        $dto = new ServiceInputDTO(
            name: 'Troca de Óleo',
            base_price: 150.00,
            category: Service::CATEGORY_MAINTENANCE,
            description: 'Troca completa de óleo e filtro',
            estimated_time: 60,
            is_active: true
        );

        $result = $this->serviceService->create($dto);

        $this->assertNotNull($result->id);
        $this->assertEquals('Troca de Óleo', $result->name);
        $this->assertEquals(150.00, $result->base_price);
        $this->assertEquals(Service::CATEGORY_MAINTENANCE, $result->category);
        $this->assertTrue($result->is_active);

        $this->assertDatabaseHas('services', [
            'name' => 'Troca de Óleo',
            'base_price' => 150.00,
            'category' => Service::CATEGORY_MAINTENANCE,
        ]);
    }

    public function testUpdateServiceSuccessfully(): void
    {
        $service = Service::factory()->create([
            'name' => 'Original Name',
            'base_price' => 100.00,
        ]);

        $dto = new ServiceInputDTO(
            name: 'Updated Name',
            base_price: 200.00,
            category: Service::CATEGORY_REPAIR,
        );

        $this->serviceService->update($service->id, $dto);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Updated Name',
            'base_price' => 200.00,
            'category' => Service::CATEGORY_REPAIR,
        ]);
    }

    public function testDeleteServiceSuccessfully(): void
    {
        $service = Service::factory()->create();

        $result = $this->serviceService->delete($service->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }

    public function testDeleteNonExistentServiceReturnsFalse(): void
    {
        $result = $this->serviceService->delete('non-existent-id');

        $this->assertFalse($result);
    }

    public function testFindServiceById(): void
    {
        $service = Service::factory()->create([
            'name' => 'Test Service',
        ]);

        $result = $this->serviceService->find($service->id);

        $this->assertNotNull($result);
        $this->assertEquals($service->id, $result->id);
        $this->assertEquals('Test Service', $result->name);
    }

    public function testFindServiceByName(): void
    {
        Service::factory()->create([
            'name' => 'Alinhamento',
        ]);

        $result = $this->serviceService->findByName('Alinhamento');

        $this->assertNotNull($result);
        $this->assertEquals('Alinhamento', $result->name);
    }

    public function testFindServiceByCategory(): void
    {
        Service::factory()->create([
            'category' => Service::CATEGORY_MAINTENANCE,
            'is_active' => true,
        ]);
        Service::factory()->create([
            'category' => Service::CATEGORY_REPAIR,
            'is_active' => true,
        ]);

        $results = $this->serviceService->findByCategory(Service::CATEGORY_MAINTENANCE);

        $this->assertCount(1, $results);
        $this->assertEquals(Service::CATEGORY_MAINTENANCE, $results->first()->category);
    }

    public function testListServicesWithSearch(): void
    {
        Service::factory()->create(['name' => 'Troca de Óleo']);
        Service::factory()->create(['name' => 'Alinhamento']);
        Service::factory()->create(['name' => 'Pintura']);

        $request = new Request(['search' => 'Óleo']);
        $searchDTO = SearchDTO::fromRequest($request);

        $results = $this->serviceService->list($searchDTO);

        $this->assertEquals(1, $results->total());
        $this->assertStringContainsString('Óleo', $results->items()[0]->name);
    }

    public function testListServicesWithCategoryFilter(): void
    {
        Service::factory()->create(['category' => Service::CATEGORY_MAINTENANCE]);
        Service::factory()->create(['category' => Service::CATEGORY_REPAIR]);
        Service::factory()->create(['category' => Service::CATEGORY_MAINTENANCE]);

        $request = new Request(['category' => Service::CATEGORY_MAINTENANCE]);
        $searchDTO = SearchDTO::fromRequest($request);

        $results = $this->serviceService->list($searchDTO);

        $this->assertGreaterThanOrEqual(2, $results->total());
        foreach ($results as $service) {
            $this->assertEquals(Service::CATEGORY_MAINTENANCE, $service->category);
        }
    }

    public function testListServicesWithPriceRangeFilter(): void
    {
        Service::factory()->create(['base_price' => 50.00]);
        Service::factory()->create(['base_price' => 150.00]);
        Service::factory()->create(['base_price' => 300.00]);

        $request = new Request([
            'min_price' => 100.00,
            'max_price' => 200.00,
        ]);
        $searchDTO = SearchDTO::fromRequest($request);

        $results = $this->serviceService->list($searchDTO);

        $this->assertGreaterThanOrEqual(1, $results->total());
        foreach ($results as $service) {
            $this->assertGreaterThanOrEqual(100.00, (float) $service->base_price);
            $this->assertLessThanOrEqual(200.00, (float) $service->base_price);
        }
    }

    public function testListOnlyActiveServices(): void
    {
        Service::factory()->create(['is_active' => true]);
        Service::factory()->create(['is_active' => false]);
        Service::factory()->create(['is_active' => true]);

        $request = new Request();
        $searchDTO = SearchDTO::fromRequest($request);

        $results = $this->serviceService->listActive($searchDTO);

        $this->assertEquals(2, $results->total());
        foreach ($results as $service) {
            $this->assertTrue($service->is_active);
        }
    }

    public function testToggleActiveStatus(): void
    {
        $service = Service::factory()->create(['is_active' => true]);

        $result = $this->serviceService->toggleActive($service->id);

        $this->assertFalse($result->is_active);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'is_active' => false,
        ]);

        $result = $this->serviceService->toggleActive($service->id);

        $this->assertTrue($result->is_active);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'is_active' => true,
        ]);
    }

    public function testListServicesWithSorting(): void
    {
        Service::factory()->create(['name' => 'Zebra Service', 'base_price' => 100.00]);
        Service::factory()->create(['name' => 'Alpha Service', 'base_price' => 300.00]);
        Service::factory()->create(['name' => 'Beta Service', 'base_price' => 200.00]);

        // Test sort by name ascending
        $request = new Request(['sort_by' => 'name', 'sort_direction' => 'asc']);
        $searchDTO = SearchDTO::fromRequest($request);
        $results = $this->serviceService->list($searchDTO);

        $this->assertEquals('Alpha Service', $results->items()[0]->name);

        // Test sort by price descending
        $request = new Request(['sort_by' => 'base_price', 'sort_direction' => 'desc']);
        $searchDTO = SearchDTO::fromRequest($request);
        $results = $this->serviceService->list($searchDTO);

        $this->assertEquals(300.00, (float) $results->items()[0]->base_price);
    }

    public function testListServicesWithPagination(): void
    {
        Service::factory()->count(15)->create();

        $request = new Request(['per_page' => 5]);
        $searchDTO = SearchDTO::fromRequest($request);

        $results = $this->serviceService->list($searchDTO);

        $this->assertCount(5, $results->items());
        $this->assertEquals(15, $results->total());
    }
}
