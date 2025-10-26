<?php

namespace Tests\Feature\Cars;

use App\Models\CarsModel;
use App\Models\ClientModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Helpers\CpfGenerator;
use Symfony\Component\Uid\Ulid;

class CarsControllerTest extends TestCase
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

    public function testIndexReturnsCarsList(): void
    {
        $response = $this->get('/cars');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'cars',
            ]);
    }

    public function testStoreCreatesCarSuccessfully(): void
    {
        $carData = [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'typeCar' => 'sedan',
            'clientId' => $this->clientId,
            'licencePlate' => 'ABC1234',
            'vin' => '1HGBH41JXMN109186',
            'color' => 'Red',
            'mileage' => 15000,
            'transmission' => 'automatic',
            'engineSize' => 1800,
            'observations' => 'Test car',
        ];

        $response = $this->post('/cars', $carData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Car created successfully',
            ])
            ->assertJsonStructure([
                'car_id',
            ]);

        $this->assertDatabaseHas('cars', [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'type' => 'sedan',
            'client_id' => $this->clientId,
        ]);
    }

    public function testStoreValidatesRequiredFields(): void
    {
        $response = $this->actingAs($this->user)->postJson('/cars', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['brand', 'model', 'year', 'typeCar', 'clientId']);
    }

    public function testShowByIdReturnsCarSuccessfully(): void
    {
        $carId = (new Ulid())->toString();
        CarsModel::create([
            'id' => $carId,
            'brand' => 'Honda',
            'model' => 'Civic',
            'year' => 2023,
            'type' => 'sedan',
            'client_id' => $this->clientId,
        ]);

        $response = $this->get("/cars/{$carId}");

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

        $response = $this->get("/cars/{$invalidId}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateModifiesCarSuccessfully(): void
    {
        $carId = (new Ulid())->toString();
        CarsModel::create([
            'id' => $carId,
            'brand' => 'Ford',
            'model' => 'Focus',
            'year' => 2021,
            'type' => 'hatchback',
            'client_id' => $this->clientId,
        ]);

        $updateData = [
            'color' => 'Blue',
            'mileage' => 25000,
        ];

        $response = $this->put("/cars/{$carId}", $updateData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Car updated successfully',
            ]);

        $this->assertDatabaseHas('cars', [
            'id' => $carId,
            'color' => 'Blue',
            'mileage' => 25000,
        ]);
    }

    public function testDeleteRemovesCarSuccessfully(): void
    {
        $carId = (new Ulid())->toString();
        CarsModel::create([
            'id' => $carId,
            'brand' => 'BMW',
            'model' => 'X5',
            'year' => 2024,
            'type' => 'suv',
            'client_id' => $this->clientId,
        ]);

        $response = $this->delete("/cars/{$carId}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Car deleted successfully',
            ]);

        $this->assertSoftDeleted('cars', [
            'id' => $carId,
        ]);
    }

    public function testDeleteReturnsNotFoundForInvalidId(): void
    {
        $invalidId = (new Ulid())->toString();

        $response = $this->delete("/cars/{$invalidId}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testFindByFiltersReturnsPaginatedResults(): void
    {
        // Create multiple cars
        for ($i = 0; $i < 5; $i++) {
            CarsModel::create([
                'id' => (new Ulid())->toString(),
                'brand' => "Brand{$i}",
                'model' => "Model{$i}",
                'year' => 2020 + $i,
                'type' => 'sedan',
                'client_id' => $this->clientId,
            ]);
        }

        $response = $this->get('/cars/filters?' . http_build_query([
            'page' => 1,
            'limit' => 3,
            'sort' => 'asc',
            'sortField' => 'brand',
            'search' => '',
        ]));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'cars' => [
                    'totalItems',
                    'items',
                ],
            ]);
    }

    public function testFindByClientIdReturnsCarsForClient(): void
    {
        // Create cars for this client
        for ($i = 0; $i < 3; $i++) {
            CarsModel::create([
                'id' => (new Ulid())->toString(),
                'brand' => "Brand{$i}",
                'model' => "Model{$i}",
                'year' => 2020 + $i,
                'type' => 'sedan',
                'client_id' => $this->clientId,
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

        CarsModel::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'OtherBrand',
            'model' => 'OtherModel',
            'year' => 2022,
            'type' => 'suv',
            'client_id' => $otherClientId,
        ]);

        $response = $this->get("/cars/client/{$this->clientId}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'cars',
            ]);

        $cars = $response->json('cars');
        $this->assertCount(3, $cars);
    }

    public function testFindByLicencePlateReturnsCarSuccessfully(): void
    {
        $carId = (new Ulid())->toString();
        CarsModel::create([
            'id' => $carId,
            'brand' => 'Volkswagen',
            'model' => 'Golf',
            'year' => 2022,
            'type' => 'hatchback',
            'licence_plate' => 'XYZ9876',
            'client_id' => $this->clientId,
        ]);

        $response = $this->get('/cars/licence-plate/XYZ9876');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'car' => [
                    'brand',
                    'model',
                    'licence_plate',
                ],
            ]);
    }

    public function testFindByLicencePlateReturnsNotFoundForInvalidPlate(): void
    {
        $response = $this->get('/cars/licence-plate/INVALID123');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testFindByVinReturnsCarSuccessfully(): void
    {
        $carId = (new Ulid())->toString();
        CarsModel::create([
            'id' => $carId,
            'brand' => 'Mercedes',
            'model' => 'C-Class',
            'year' => 2023,
            'type' => 'sedan',
            'vin' => 'WBA3B1C50DF123456',
            'client_id' => $this->clientId,
        ]);

        $response = $this->get('/cars/vin/WBA3B1C50DF123456');

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
        $response = $this->get('/cars/vin/INVALIDVIN123456');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testStoreValidatesLicencePlateFormat(): void
    {
        $carData = [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'typeCar' => 'sedan',
            'clientId' => $this->clientId,
            'licencePlate' => 'INVALID',
        ];

        $response = $this->actingAs($this->user)->postJson('/cars', $carData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['licencePlate']);
    }

    public function testStoreValidatesVinFormat(): void
    {
        $carData = [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'typeCar' => 'sedan',
            'clientId' => $this->clientId,
            'vin' => 'INVALID',
        ];

        $response = $this->actingAs($this->user)->postJson('/cars', $carData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['vin']);
    }

    public function testStoreValidatesYearRange(): void
    {
        $carData = [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 1800, // Invalid year
            'typeCar' => 'sedan',
            'clientId' => $this->clientId,
        ];

        $response = $this->actingAs($this->user)->postJson('/cars', $carData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['year']);
    }

    public function testStoreValidatesCarType(): void
    {
        $carData = [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'typeCar' => 'invalid_type',
            'clientId' => $this->clientId,
        ];

        $response = $this->actingAs($this->user)->postJson('/cars', $carData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['typeCar']);
    }
}
