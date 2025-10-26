<?php

declare(strict_types=1);

namespace Tests\Feature\Cars;

use App\Models\CarsModel;
use App\Models\ClientModel;
use AppOficina\Cars\Entities\Car;
use AppOficina\Cars\Repository\CarRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Uid\Ulid;
use Tests\TestCase;

class CarEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private CarRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(CarRepositoryInterface::class);
    }

    public function testItSavesACar(): void
    {
        $clientId = new Ulid();

        // Create client first to satisfy foreign key constraint
        ClientModel::create([
            'id' => $clientId->toString(),
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'document_number' => '12345678901',
        ]);

        $car = Car::create(
            brand: 'Toyota',
            model: 'Corolla',
            year: 2022,
            typeCar: 'sedan',
            clientId: $clientId
        );

        $this->repository->save($car);

        $this->assertDatabaseHas('cars', [
            'id' => $car->getId()->toString(),
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'type' => 'sedan',
            'client_id' => $clientId->toString(),
        ]);
    }

    public function testItFindsCarById(): void
    {
        $clientId = new Ulid();
        $carId = new Ulid();

        // Create client first to satisfy foreign key constraint
        ClientModel::create([
            'id' => $clientId->toString(),
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'document_number' => '12345678901',
        ]);

        CarsModel::create([
            'id' => $carId->toString(),
            'brand' => 'Honda',
            'model' => 'Civic',
            'year' => 2023,
            'type' => 'sedan',
            'client_id' => $clientId->toString(),
        ]);

        /** @var Car $car */
        $car = $this->repository->findById($carId);

        $this->assertNotNull($car);
        $this->assertEquals('Honda', $car->brand);
        $this->assertEquals('Civic', $car->model);
        $this->assertEquals(2023, $car->year);
        $this->assertEquals($clientId->toString(), $car->clientId->toString());
    }

    public function testItFindsCarByLicencePlate(): void
    {
        $clientId = new Ulid();
        $carId = new Ulid();

        // Create client first to satisfy foreign key constraint
        ClientModel::create([
            'id' => $clientId->toString(),
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'document_number' => '12345678901',
        ]);

        CarsModel::create([
            'id' => $carId->toString(),
            'brand' => 'Ford',
            'model' => 'Focus',
            'year' => 2021,
            'type' => 'hatchback',
            'licence_plate' => 'ABC1234',
            'client_id' => $clientId->toString(),
        ]);

        $car = $this->repository->findByLicencePlate('ABC1234');

        $this->assertNotNull($car);
        $this->assertEquals('Ford', $car->brand);
        $this->assertEquals('ABC1234', $car->licencePlate->value());
    }

    public function testItFindsCarByVin(): void
    {
        $clientId = new Ulid();
        $carId = new Ulid();

        // Create client first to satisfy foreign key constraint
        ClientModel::create([
            'id' => $clientId->toString(),
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'document_number' => '12345678901',
        ]);

        CarsModel::create([
            'id' => $carId->toString(),
            'brand' => 'BMW',
            'model' => 'X5',
            'year' => 2024,
            'type' => 'suv',
            'vin' => '1HGCM82633A123456',
            'client_id' => $clientId->toString(),
        ]);

        $car = $this->repository->findByVin('1HGCM82633A123456');

        $this->assertNotNull($car);
        $this->assertEquals('BMW', $car->brand);
        $this->assertEquals('1HGCM82633A123456', $car->vin->value());
    }

    public function testItFindsCarsByClientId(): void
    {
        $clientId = new Ulid();
        $otherClientId = new Ulid();

        // Create clients first to satisfy foreign key constraints
        ClientModel::create([
            'id' => $clientId->toString(),
            'name' => 'Test Client 1',
            'email' => 'test1@example.com',
            'document_number' => '12345678901',
        ]);

        ClientModel::create([
            'id' => $otherClientId->toString(),
            'name' => 'Test Client 2',
            'email' => 'test2@example.com',
            'document_number' => '98765432100',
        ]);

        CarsModel::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'type' => 'sedan',
            'client_id' => $clientId->toString(),
        ]);

        CarsModel::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'Honda',
            'model' => 'Civic',
            'year' => 2023,
            'type' => 'sedan',
            'client_id' => $clientId->toString(),
        ]);

        CarsModel::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'Ford',
            'model' => 'Focus',
            'year' => 2021,
            'type' => 'hatchback',
            'client_id' => $otherClientId->toString(),
        ]);

        $cars = $this->repository->findByClientId($clientId);

        $this->assertCount(2, $cars);
        $this->assertEquals('Toyota', $cars[0]->brand);
        $this->assertEquals('Honda', $cars[1]->brand);
    }

    public function testItUpdatesACar(): void
    {
        $clientId = new Ulid();

        // Create client first to satisfy foreign key constraint
        ClientModel::create([
            'id' => $clientId->toString(),
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'document_number' => '12345678901',
        ]);

        $car = Car::create(
            brand: 'Toyota',
            model: 'Corolla',
            year: 2022,
            typeCar: 'sedan',
            clientId: $clientId
        );

        $this->repository->save($car);

        $updatedCar = $car
            ->withColor('Red')
            ->withMileage(15000);

        $this->repository->update($updatedCar);

        $this->assertDatabaseHas('cars', [
            'id' => $car->getId()->toString(),
            'color' => 'Red',
            'mileage' => 15000,
        ]);
    }

    public function testItDeletesACar(): void
    {
        $clientId = new Ulid();

        // Create client first to satisfy foreign key constraint
        ClientModel::create([
            'id' => $clientId->toString(),
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'document_number' => '12345678901',
        ]);

        $car = Car::create(
            brand: 'Toyota',
            model: 'Corolla',
            year: 2022,
            typeCar: 'sedan',
            clientId: $clientId
        );

        $this->repository->save($car);
        $this->repository->delete($car->getId());

        $this->assertSoftDeleted('cars', [
            'id' => $car->getId()->toString(),
        ]);
    }
}
