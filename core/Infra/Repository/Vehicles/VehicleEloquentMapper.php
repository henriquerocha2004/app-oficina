<?php

namespace AppOficina\Infra\Repository\Vehicles;

use AppOficina\Vehicles\Entities\Vehicle;
use AppOficina\Shared\Entity\Entity;
use AppOficina\Shared\Mapper\MapperInterface;
use Symfony\Component\Uid\Ulid;

use function PHPSTORM_META\type;

class VehicleEloquentMapper implements MapperInterface
{
    public function toDomain(object $persistenceModel): Entity
    {
        $car = Vehicle::create(
            brand: $persistenceModel->brand,
            model: $persistenceModel->model,
            year: $persistenceModel->year,
            vehicleType: $persistenceModel->vehicle_type,
            clientId: Ulid::fromString($persistenceModel->client_id),
            id: Ulid::fromString($persistenceModel->id)
        );

        if ($persistenceModel->license_plate) {
            $car = $car->withLicensePlate($persistenceModel->license_plate);
        }

        if ($persistenceModel->vin) {
            $car = $car->withVin($persistenceModel->vin);
        }

        if ($persistenceModel->transmission) {
            $car = $car->withTransmission($persistenceModel->transmission);
        }

        if ($persistenceModel->color) {
            $car = $car->withColor($persistenceModel->color);
        }

        if ($persistenceModel->cilinder_capacity) {
            $car = $car->withCilinderCapacity($persistenceModel->cilinder_capacity);
        }

        if ($persistenceModel->mileage) {
            $car = $car->withMileage($persistenceModel->mileage);
        }

        if ($persistenceModel->observations) {
            $car = $car->withObservations($persistenceModel->observations);
        }

        return $car;
    }

    /** @param Vehicle $domainModel */
    public function toPersistence(Entity $domainModel): array
    {
        if (!$domainModel instanceof Vehicle) {
            throw new \InvalidArgumentException('Invalid domain model type');
        }

        return [
            'id' => $domainModel->getId()->toString(),
            'client_id' => $domainModel->clientId->toString(),
            'brand' => $domainModel->brand,
            'model' => $domainModel->model,
            'year' => $domainModel->year,
            'vehicle_type' => $domainModel->type->value,
            'license_plate' => $domainModel->licensePlate ? $domainModel->licensePlate->value() : null,
            'vin' => $domainModel->vin ? $domainModel->vin->value() : null,
            'transmission' => $domainModel->transmission ? $domainModel->transmission->value : null,
            'color' => $domainModel->color ?: null,
            'cilinder_capacity' => $domainModel->cilinderCapacity ?: null,
            'mileage' => $domainModel->mileage ?: null,
            'observations' => $domainModel->observations ?: null,
        ];
    }
}
