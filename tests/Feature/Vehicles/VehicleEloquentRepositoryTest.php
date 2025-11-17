<?php

declare(strict_types=1);

namespace Tests\Feature\Vehicles;

use App\Models\VehicleModel;
use App\Models\ClientModel;
use AppOficina\Vehicles\Entities\Vehicle;
use AppOficina\Vehicles\Repository\VehicleRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Uid\Ulid;
use Tests\TestCase;

class VehicleEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private VehicleRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(VehicleRepositoryInterface::class);
    }

    public function testItSavesAVehicle(): void
    {
        $clientId = new Ulid();

        // Create client first to satisfy foreign key constraint
        ClientModel::create([
            'id' => $clientId->toString(),
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'document_number' => '12345678901',
        ]);

        $car = Vehicle::create(
            brand: 'Toyota',
            model: 'Corolla',
            year: 2022,
            vehicleType: "car",
            clientId: $clientId
        );

        $this->repository->save($car);

        $this->assertDatabaseHas('vehicles', [
            'id' => $car->getId()->toString(),
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'vehicle_type' => 'car',
            'client_id' => $clientId->toString(),
        ]);
    }

    public function testItFindsVehicleById(): void
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

        VehicleModel::create([
            'id' => $carId->toString(),
            'brand' => 'Honda',
            'model' => 'Civic',
            'year' => 2023,
            'vehicle_type' => 'car',
            'client_id' => $clientId->toString(),
            'vehicle_type' => 'car',
        ]);

        /** @var Vehicle $car */
        $car = $this->repository->findById($carId);

        $this->assertNotNull($car);
        $this->assertEquals('Honda', $car->brand);
        $this->assertEquals('Civic', $car->model);
        $this->assertEquals(2023, $car->year);
        $this->assertEquals($clientId->toString(), $car->clientId->toString());
    }

    public function testItFindsVehicleByLicencePlate(): void
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

        VehicleModel::create([
            'id' => $carId->toString(),
            'brand' => 'Ford',
            'model' => 'Focus',
            'year' => 2021,
            'vehicle_type' => 'car',
            'license_plate' => 'ABC1234',
            'client_id' => $clientId->toString(),
            'vehicle_type' => 'car',
        ]);

        $car = $this->repository->findByLicencePlate('ABC1234');

        $this->assertNotNull($car);
        $this->assertEquals('Ford', $car->brand);
        $this->assertEquals('ABC1234', $car->licencePlate->value());
    }

    public function testItFindsVehicleByVin(): void
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

        VehicleModel::create([
            'id' => $carId->toString(),
            'brand' => 'BMW',
            'model' => 'X5',
            'year' => 2024,
            'vehicle_type' => 'car',
            'vin' => '1HGCM82633A123456',
            'client_id' => $clientId->toString(),
            'vehicle_type' => 'car',
        ]);

        $car = $this->repository->findByVin('1HGCM82633A123456');

        $this->assertNotNull($car);
        $this->assertEquals('BMW', $car->brand);
        $this->assertEquals('1HGCM82633A123456', $car->vin->value());
    }

    public function testItFindsVehiclesByClientId(): void
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

        VehicleModel::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'vehicle_type' => 'car',
            'client_id' => $clientId->toString(),
            'vehicle_type' => 'car',
        ]);

        VehicleModel::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'Honda',
            'model' => 'Civic',
            'year' => 2023,
            'vehicle_type' => 'car',
            'client_id' => $clientId->toString(),
            'vehicle_type' => 'car',
        ]);

        VehicleModel::create([
            'id' => (new Ulid())->toString(),
            'brand' => 'Ford',
            'model' => 'Focus',
            'year' => 2021,
            'vehicle_type' => 'car',
            'client_id' => $otherClientId->toString(),
            'vehicle_type' => 'car',
        ]);

        $vehicles = $this->repository->findByClientId($clientId);

        $this->assertCount(2, $vehicles);
        $this->assertEquals('Toyota', $vehicles[0]->brand);
        $this->assertEquals('Honda', $vehicles[1]->brand);
    }

    public function testItUpdatesAVehicle(): void
    {
        $clientId = new Ulid();

        // Create client first to satisfy foreign key constraint
        ClientModel::create([
            'id' => $clientId->toString(),
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'document_number' => '12345678901',
        ]);

        $car = Vehicle::create(
            brand: 'Toyota',
            model: 'Corolla',
            year: 2022,
            vehicleType: "car",
            clientId: $clientId
        );

        $this->repository->save($car);

        $updatedVehicle = $car
            ->withColor('Red')
            ->withMileage(15000);

        $this->repository->update($updatedVehicle);

        $this->assertDatabaseHas('vehicles', [
            'id' => $car->getId()->toString(),
            'color' => 'Red',
            'mileage' => 15000,
        ]);
    }

    public function testItDeletesAVehicle(): void
    {
        $clientId = new Ulid();

        // Create client first to satisfy foreign key constraint
        ClientModel::create([
            'id' => $clientId->toString(),
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'document_number' => '12345678901',
        ]);

        $car = Vehicle::create(
            brand: 'Toyota',
            model: 'Corolla',
            year: 2022,
            vehicleType: "car",
            clientId: $clientId
        );

        $this->repository->save($car);
        $this->repository->delete($car->getId());

        $this->assertSoftDeleted('vehicles', [
            'id' => $car->getId()->toString(),
        ]);
    }
}
