<?php

namespace Tests\Feature\Vehicles;

use App\Models\Vehicle;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Helpers\CpfGenerator;
use Tests\Helpers\TenantTestHelper;
use Symfony\Component\Uid\Ulid;

class VehiclesControllerTest extends TestCase
{
    use RefreshDatabase;
    use CpfGenerator;
    use TenantTestHelper;

    private string $clientId;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize tenant for testing
        $this->initializeTenant();

        // Create and authenticate a test user within tenant context
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Create a test client within tenant context
        $this->clientId = (new Ulid())->toString();
        Client::create([
            'id' => $this->clientId,
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'document_number' => $this->generateValidCpf(),
        ]);
    }

    public function testStoreCreatesVehicleSuccessfully(): void
    {
        $carData = [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'client_id' => $this->clientId,
            'plate' => 'ABC1234',
            'color' => 'Red',
        ];

        $response = $this->post('/vehicles', $carData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Vehicle created successfully',
            ])
            ->assertJsonStructure([
                'vehicle_id',
            ]);

        $this->assertDatabaseHas('vehicles', [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'client_id' => $this->clientId,
            'license_plate' => 'ABC1234',
        ]);
    }

    public function testStoreValidatesRequiredFields(): void
    {
        $response = $this->actingAs($this->user)->postJson('/vehicles', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['brand', 'model', 'year', 'plate', 'client_id']);
    }

    public function testShowByIdReturnsVehicleSuccessfully(): void
    {
        $carId = (new Ulid())->toString();
        Vehicle::create([
            'id' => $carId,
            'brand' => 'Honda',
            'model' => 'Civic',
            'year' => 2023,
            'license_plate' => 'TEST123',
            'vehicle_type' => 'car',
            'client_id' => $this->clientId,
        ]);

        $response = $this->get("/vehicles/{$carId}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'vehicle' => [
                    'brand',
                    'model',
                    'year',
                ],
            ]);
    }

    public function testShowByIdReturnsVehicleWithClientInfo(): void
    {
        $carId = (new Ulid())->toString();
        Vehicle::create([
            'id' => $carId,
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'license_plate' => 'ABC1234',
            'vehicle_type' => 'car',
            'color' => 'Prata',
            'fuel' => 'gasoline',
            'transmission' => 'automatic',
            'mileage' => 45000,
            'observations' => 'Veículo em excelente estado',
            'client_id' => $this->clientId,
        ]);

        $response = $this->get("/vehicles/{$carId}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'vehicle' => [
                    'id',
                    'brand',
                    'model',
                    'year',
                    'plate',
                    'color',
                    'vehicle_type',
                    'fuel',
                    'transmission',
                    'mileage',
                    'observations',
                    'client' => [
                        'id',
                        'name',
                        'email',
                        'document_number',
                        'phone',
                    ],
                ],
            ])
            ->assertJson([
                'vehicle' => [
                    'brand' => 'Toyota',
                    'model' => 'Corolla',
                    'year' => 2020,
                    'plate' => 'ABC1234',
                    'color' => 'Prata',
                ],
            ]);
    }

    public function testShowByIdReturnsNotFoundForInvalidId(): void
    {
        $invalidId = (new Ulid())->toString();

        $response = $this->get("/vehicles/{$invalidId}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateModifiesVehicleSuccessfully(): void
    {
        $carId = (new Ulid())->toString();
        Vehicle::create([
            'id' => $carId,
            'brand' => 'Ford',
            'model' => 'Focus',
            'year' => 2021,
            'license_plate' => 'FORD123',
            'vehicle_type' => 'car',
            'client_id' => $this->clientId,
        ]);

        $updateData = [
            'brand' => 'Ford',
            'model' => 'Focus',
            'year' => 2021,
            'client_id' => $this->clientId,
            'plate' => 'ABC1234',
            'color' => 'Blue',
        ];

        $response = $this->put("/vehicles/{$carId}", $updateData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Vehicle updated successfully',
            ]);

        $this->assertDatabaseHas('vehicles', [
            'id' => $carId,
            'color' => 'Blue',
        ]);
    }

    public function testUpdateModifiesVehicleWithTechnicalInfo(): void
    {
        $carId = (new Ulid())->toString();
        Vehicle::create([
            'id' => $carId,
            'brand' => 'Honda',
            'model' => 'Civic',
            'year' => 2022,
            'license_plate' => 'HONDA123',
            'vehicle_type' => 'car',
            'client_id' => $this->clientId,
        ]);

        $updateData = [
            'brand' => 'Honda',
            'model' => 'Civic',
            'year' => 2022,
            'client_id' => $this->clientId,
            'plate' => 'ABC1D23',
            'color' => 'Silver',
            'vehicle_type' => 'car',
            'cilinder_capacity' => '2000',
            'vin' => '1HGBH41JXMN109186',
            'observations' => 'Updated with technical info',
        ];

        $response = $this->put("/vehicles/{$carId}", $updateData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Vehicle updated successfully',
            ]);

        $this->assertDatabaseHas('vehicles', [
            'id' => $carId,
            'license_plate' => 'ABC1D23',
            'color' => 'Silver',
            'cilinder_capacity' => '2000',
            'vin' => '1HGBH41JXMN109186',
            'observations' => 'Updated with technical info',
        ]);
    }

    public function testDeleteRemovesVehicleSuccessfully(): void
    {
        $carId = (new Ulid())->toString();
        Vehicle::create([
            'id' => $carId,
            'brand' => 'BMW',
            'model' => 'X5',
            'year' => 2024,
            'license_plate' => 'BMW1234',
            'vehicle_type' => 'car',
            'client_id' => $this->clientId,
        ]);

        $response = $this->delete("/vehicles/{$carId}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Vehicle deleted successfully',
            ]);

        $this->assertSoftDeleted('vehicles', [
            'id' => $carId,
        ]);
    }

    public function testDeleteReturnsNotFoundForInvalidId(): void
    {
        $invalidId = (new Ulid())->toString();

        $response = $this->delete("/vehicles/{$invalidId}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testFindByFiltersReturnsPaginatedResults(): void
    {
        // Create multiple vehicles
        for ($i = 0; $i < 5; $i++) {
            Vehicle::create([
                'id' => (new Ulid())->toString(),
                'brand' => "Brand{$i}",
                'model' => "Model{$i}",
                'year' => 2020 + $i,
                'license_plate' => "PLT{$i}123",
                'vehicle_type' => 'car',
                'client_id' => $this->clientId,
            ]);
        }

        $response = $this->get('/vehicles/filters?' . http_build_query([
            'page' => 1,
            'per_page' => 3,
            'sort_direction' => 'asc',
            'sort_by' => 'brand',
            'search' => '',
        ]));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'vehicles' => [
                    'total_items',
                    'items',
                ],
            ]);
    }

    public function testFindByClientIdReturnsVehiclesForClient(): void
    {
        // Create vehicles for this client
        for ($i = 0; $i < 3; $i++) {
            Vehicle::create([
                'id' => (new Ulid())->toString(),
                'brand' => "Brand{$i}",
                'model' => "Model{$i}",
                'year' => 2020 + $i,
                'license_plate' => "CLI{$i}123",
                'vehicle_type' => 'car',
                'client_id' => $this->clientId,
            ]);
        }

        // Create car for different client
        $otherClientId = (new Ulid())->toString();
        Client::create([
            'id' => $otherClientId,
            'name' => 'Other Client',
            'email' => 'other@example.com',
            'document_number' => '98765432100',
        ]);

        Vehicle::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'OtherBrand',
            'model' => 'OtherModel',
            'year' => 2022,
            'license_plate' => 'OTHER99',
            'vehicle_type' => 'car',
            'client_id' => $otherClientId,
        ]);

        $response = $this->get("/vehicles/client/{$this->clientId}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'vehicles',
            ]);

        $vehicles = $response->json('vehicles');
        $this->assertCount(3, $vehicles);
    }

    public function testFindByClientIdReturnsEmptyWhenNoVehicles(): void
    {
        $emptyClientId = (new Ulid())->toString();
        Client::create([
            'id' => $emptyClientId,
            'name' => 'Client Without Vehicles',
            'email' => 'empty@example.com',
            'document_number' => '11111111111',
        ]);

        $response = $this->get("/vehicles/client/{$emptyClientId}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'vehicles' => [],
            ]);
    }

    public function testFindByLicencePlateReturnsVehicleSuccessfully(): void
    {
        $carId = (new Ulid())->toString();
        Vehicle::create([
            'id' => $carId,
            'brand' => 'Volkswagen',
            'model' => 'Golf',
            'year' => 2022,
            'vehicle_type' => 'car',
            'license_plate' => 'XYZ9876',
            'client_id' => $this->clientId,
            'vehicle_type' => 'car',
        ]);

        $response = $this->get('/vehicles/licence-plate/XYZ9876');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'vehicle' => [
                    'brand',
                    'model',
                    'plate',
                ],
            ]);
    }

    public function testFindByLicencePlateReturnsNotFoundForInvalidPlate(): void
    {
        $response = $this->get('/vehicles/licence-plate/INVALID123');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testFindByVinReturnsVehicleSuccessfully(): void
    {
        $carId = (new Ulid())->toString();
        Vehicle::create([
            'id' => $carId,
            'brand' => 'Mercedes',
            'model' => 'C-Class',
            'year' => 2023,
            'license_plate' => 'MERC123',
            'vehicle_type' => 'car',
            'vin' => 'WBA3B1C50DF123456',
            'client_id' => $this->clientId,
        ]);

        $response = $this->get('/vehicles/vin/WBA3B1C50DF123456');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'vehicle' => [
                    'brand',
                    'model',
                ],
            ]);
    }

    public function testFindByVinReturnsNotFoundForInvalidVin(): void
    {
        $response = $this->get('/vehicles/vin/INVALIDVIN123456');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testStoreValidatesPlateFormat(): void
    {
        $carData = [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'client_id' => $this->clientId,
            'plate' => 'INVALID',
            'color' => 'Red',
        ];

        $response = $this->actingAs($this->user)->postJson('/vehicles', $carData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['plate']);
    }

    public function testStoreValidatesYearRange(): void
    {
        $carData = [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 1800, // Invalid year
            'client_id' => $this->clientId,
            'plate' => 'ABC1234',
            'color' => 'Red',
        ];

        $response = $this->actingAs($this->user)->postJson('/vehicles', $carData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['year']);
    }

    public function testFilterByVehicleTypeReturnsOnlyCars(): void
    {
        // Create cars
        Vehicle::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'license_plate' => 'CAR1234',
            'vehicle_type' => 'car',
            'client_id' => $this->clientId,
        ]);

        // Create motorcycles
        Vehicle::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'Honda',
            'model' => 'CB500',
            'year' => 2021,
            'license_plate' => 'MOT5678',
            'vehicle_type' => 'motorcycle',
            'client_id' => $this->clientId,
        ]);

        $response = $this->get('/vehicles/filters?' . http_build_query([
            'page' => 1,
            'per_page' => 10,
            'sort_direction' => 'asc',
            'sort_by' => 'brand',
            'vehicle_type' => 'car',
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $data = $response->json('vehicles.items');

        $this->assertNotEmpty($data);
        foreach ($data as $vehicle) {
            $this->assertEquals('car', $vehicle['vehicle_type']);
        }
    }

    public function testFilterByVehicleTypeReturnsOnlyMotorcycles(): void
    {
        // Create cars
        Vehicle::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'license_plate' => 'CAR1234',
            'vehicle_type' => 'car',
            'client_id' => $this->clientId,
        ]);

        // Create motorcycles
        Vehicle::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'Honda',
            'model' => 'CB500',
            'year' => 2021,
            'license_plate' => 'MOT5678',
            'vehicle_type' => 'motorcycle',
            'client_id' => $this->clientId,
        ]);

        $response = $this->get('/vehicles/filters?' . http_build_query([
            'page' => 1,
            'per_page' => 10,
            'sort_direction' => 'asc',
            'sort_by' => 'brand',
            'vehicle_type' => 'motorcycle',
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $data = $response->json('vehicles.items');

        $this->assertNotEmpty($data);
        foreach ($data as $vehicle) {
            $this->assertEquals('motorcycle', $vehicle['vehicle_type']);
        }
    }

    public function testSearchByClientNameReturnsVehicles(): void
    {
        // Create client with specific name
        $specificClientId = (new Ulid())->toString();
        Client::create([
            'id' => $specificClientId,
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'document_number' => $this->generateValidCpf(),
        ]);

        // Create vehicle for João Silva
        Vehicle::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'Volkswagen',
            'model' => 'Gol',
            'year' => 2020,
            'license_plate' => 'JOA1234',
            'vehicle_type' => 'car',
            'client_id' => $specificClientId,
        ]);

        // Create vehicle for default test client
        Vehicle::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'Fiat',
            'model' => 'Uno',
            'year' => 2019,
            'license_plate' => 'TST5678',
            'vehicle_type' => 'car',
            'client_id' => $this->clientId,
        ]);

        $response = $this->get('/vehicles/filters?' . http_build_query([
            'page' => 1,
            'per_page' => 10,
            'sort_direction' => 'asc',
            'sort_by' => 'brand',
            'search' => 'João',
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $data = $response->json('vehicles.items');

        $this->assertNotEmpty($data);
        $this->assertCount(1, $data);
        $this->assertEquals('JOA1234', $data[0]['plate']);
    }

    public function testSearchByClientNamePartialMatch(): void
    {
        // Create client with specific name
        $specificClientId = (new Ulid())->toString();
        Client::create([
            'id' => $specificClientId,
            'name' => 'Maria Santos Oliveira',
            'email' => 'maria@example.com',
            'document_number' => $this->generateValidCpf(),
        ]);

        // Create vehicle for Maria
        Vehicle::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'Chevrolet',
            'model' => 'Onix',
            'year' => 2021,
            'license_plate' => 'MAR1234',
            'vehicle_type' => 'car',
            'client_id' => $specificClientId,
        ]);

        $response = $this->get('/vehicles/filters?' . http_build_query([
            'page' => 1,
            'per_page' => 10,
            'sort_direction' => 'asc',
            'sort_by' => 'brand',
            'search' => 'Santos',
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $data = $response->json('vehicles.items');

        $this->assertNotEmpty($data);
        $this->assertEquals('MAR1234', $data[0]['plate']);
    }
}
