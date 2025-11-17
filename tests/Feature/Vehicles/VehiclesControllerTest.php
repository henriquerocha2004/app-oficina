<?php

namespace Tests\Feature\Vehicles;

use App\Models\VehicleModel;
use App\Models\ClientModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Helpers\CpfGenerator;
use Symfony\Component\Uid\Ulid;

class VehiclesControllerTest extends TestCase
{
    use RefreshDatabase;
    use CpfGenerator;

    private string $clientId;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate a test user
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Create a test client
        $this->clientId = (new Ulid())->toString();
        ClientModel::create([
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
            'typeVehicle' => 'car',
            'clientId' => $this->clientId,
            'licencePlate' => 'ABC1234',
            'vin' => '1HGBH41JXMN109186',
            'color' => 'Red',
            'mileage' => 15000,
            'transmission' => 'automatic',
            'engineSize' => 1800,
            'observations' => 'Test car',
        ];

        $response = $this->post('/vehicles', $carData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Vehicle created successfully',
            ])
            ->assertJsonStructure([
                'car_id',
            ]);

        $this->assertDatabaseHas('vehicles', [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'vehicle_type' => 'car',
            'client_id' => $this->clientId,
        ]);
    }

    public function testStoreValidatesRequiredFields(): void
    {
        $response = $this->actingAs($this->user)->postJson('/vehicles', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['brand', 'model', 'year', 'typeVehicle', 'clientId']);
    }

    public function testShowByIdReturnsVehicleSuccessfully(): void
    {
        $carId = (new Ulid())->toString();
        VehicleModel::create([
            'id' => $carId,
            'brand' => 'Honda',
            'model' => 'Civic',
            'year' => 2023,
            'vehicle_type' => 'car',
            'client_id' => $this->clientId,
            'vehicle_type' => 'car',
        ]);

        $response = $this->get("/vehicles/{$carId}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'car' => [
                    'brand',
                    'model',
                    'year',
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
        VehicleModel::create([
            'id' => $carId,
            'brand' => 'Ford',
            'model' => 'Focus',
            'year' => 2021,
            'vehicle_type' => 'car',
            'client_id' => $this->clientId,
            'vehicle_type' => 'car',
        ]);

        $updateData = [
            'color' => 'Blue',
            'mileage' => 25000,
        ];

        $response = $this->put("/vehicles/{$carId}", $updateData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Vehicle updated successfully',
            ]);

        $this->assertDatabaseHas('vehicles', [
            'id' => $carId,
            'color' => 'Blue',
            'mileage' => 25000,
        ]);
    }

    public function testDeleteRemovesVehicleSuccessfully(): void
    {
        $carId = (new Ulid())->toString();
        VehicleModel::create([
            'id' => $carId,
            'brand' => 'BMW',
            'model' => 'X5',
            'year' => 2024,
            'vehicle_type' => 'car',
            'client_id' => $this->clientId,
            'vehicle_type' => 'car',
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
            VehicleModel::create([
                'id' => (new Ulid())->toString(),
                'brand' => "Brand{$i}",
                'model' => "Model{$i}",
                'year' => 2020 + $i,
                'vehicle_type' => 'car',
                'client_id' => $this->clientId,
                'vehicle_type' => 'car',
            ]);
        }

        $response = $this->get('/vehicles/filters?' . http_build_query([
            'page' => 1,
            'limit' => 3,
            'sort' => 'asc',
            'sortField' => 'brand',
            'search' => '',
        ]));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'vehicles' => [
                    'totalItems',
                    'items',
                ],
            ]);
    }

    public function testFindByClientIdReturnsVehiclesForClient(): void
    {
        // Create vehicles for this client
        for ($i = 0; $i < 3; $i++) {
            VehicleModel::create([
                'id' => (new Ulid())->toString(),
                'brand' => "Brand{$i}",
                'model' => "Model{$i}",
                'year' => 2020 + $i,
                'vehicle_type' => 'car',
                'client_id' => $this->clientId,
                'vehicle_type' => 'car',
            ]);
        }

        // Create car for different client
        $otherClientId = (new Ulid())->toString();
        ClientModel::create([
            'id' => $otherClientId,
            'name' => 'Other Client',
            'email' => 'other@example.com',
            'document_number' => '98765432100',
        ]);

        VehicleModel::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'OtherBrand',
            'model' => 'OtherModel',
            'year' => 2022,
            'vehicle_type' => 'car',
            'client_id' => $otherClientId,
            'vehicle_type' => 'car',
        ]);

        $response = $this->get("/vehicles/client/{$this->clientId}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'vehicles',
            ]);

        $vehicles = $response->json('vehicles');
        $this->assertCount(3, $vehicles);
    }

    public function testFindByLicencePlateReturnsVehicleSuccessfully(): void
    {
        $carId = (new Ulid())->toString();
        VehicleModel::create([
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
                'car' => [
                    'brand',
                    'model',
                    'license_plate',
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
        VehicleModel::create([
            'id' => $carId,
            'brand' => 'Mercedes',
            'model' => 'C-Class',
            'year' => 2023,
            'vehicle_type' => 'car',
            'vin' => 'WBA3B1C50DF123456',
            'client_id' => $this->clientId,
            'vehicle_type' => 'car',
        ]);

        $response = $this->get('/vehicles/vin/WBA3B1C50DF123456');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'car' => [
                    'brand',
                    'model',
                    'vin',
                ],
            ]);
    }

    public function testFindByVinReturnsNotFoundForInvalidVin(): void
    {
        $response = $this->get('/vehicles/vin/INVALIDVIN123456');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testStoreValidatesLicencePlateFormat(): void
    {
        $carData = [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'typeVehicle' => 'car',
            'clientId' => $this->clientId,
            'licencePlate' => 'INVALID',
        ];

        $response = $this->actingAs($this->user)->postJson('/vehicles', $carData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['licencePlate']);
    }

    public function testStoreValidatesVinFormat(): void
    {
        $carData = [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'typeVehicle' => 'car',
            'clientId' => $this->clientId,
            'vin' => 'INVALID',
        ];

        $response = $this->actingAs($this->user)->postJson('/vehicles', $carData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['vin']);
    }

    public function testStoreValidatesYearRange(): void
    {
        $carData = [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 1800, // Invalid year
            'typeVehicle' => 'car',
            'clientId' => $this->clientId,
        ];

        $response = $this->actingAs($this->user)->postJson('/vehicles', $carData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['year']);
    }

    public function testStoreValidatesVehicleType(): void
    {
        $carData = [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'typeVehicle' => 'invalid_type',
            'clientId' => $this->clientId,
        ];

        $response = $this->actingAs($this->user)->postJson('/vehicles', $carData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['typeVehicle']);
    }
}
