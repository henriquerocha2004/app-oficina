<?php

declare(strict_types=1);

namespace AppOficina\Vehicles\UseCases\CreateVehicle;

use AppOficina\Vehicles\Entities\Vehicle;
use AppOficina\Vehicles\Repository\VehicleRepositoryInterface;

class CreateVehicleUseCase
{
    public function __construct(
        private readonly VehicleRepositoryInterface $repository
    ) {
    }

    public function execute(Input $data): Output
    {
        $existingVehicle = $this->findExistingVehicle($data);
        if ($existingVehicle !== null) {
            return new Output(carId: $existingVehicle->getId()->__toString());
        }

        $car = $this->createNewVehicle($data);
        $this->repository->save($car);

        return new Output(carId: $car->getId()->__toString());
    }

    private function findExistingVehicle(Input $data): ?Vehicle
    {
        if ($data->licensePlate !== null) {
            $existingVehicle = $this->repository->findByLicensePlate($data->licensePlate);
            if ($existingVehicle !== null) {
                return $existingVehicle;
            }
        }

        if ($data->vin !== null) {
            $existingVehicle = $this->repository->findByVin($data->vin);
            if ($existingVehicle !== null) {
                return $existingVehicle;
            }
        }

        return null;
    }

    private function createNewVehicle(Input $data): Vehicle
    {
        $car = Vehicle::create(
            brand: $data->brand,
            model: $data->model,
            year: $data->year,
            vehicleType: $data->type,
            clientId: \Symfony\Component\Uid\Ulid::fromString($data->clientId),
        );

        return $this->applyOptionalAttributes($car, $data);
    }

    private function applyOptionalAttributes(Vehicle $car, Input $data): Vehicle
    {
        if ($data->licensePlate !== null) {
            $car = $car->withLicensePlate($data->licensePlate);
        }

        if ($data->vin !== null) {
            $car = $car->withVin($data->vin);
        }

        if ($data->transmission !== null) {
            $car = $car->withTransmission($data->transmission);
        }

        if ($data->color !== null) {
            $car = $car->withColor($data->color);
        }

        if ($data->cilinderCapacity !== null) {
            $car = $car->withCilinderCapacity($data->cilinderCapacity);
        }

        if ($data->mileage !== null) {
            $car = $car->withMileage($data->mileage);
        }

        if ($data->observations !== null) {
            $car = $car->withObservations($data->observations);
        }

        return $car;
    }
}
