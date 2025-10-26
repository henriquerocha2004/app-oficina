<?php

namespace AppOficina\Infra\Repository\Cars;

use AppOficina\Cars\Entities\Car;
use AppOficina\Shared\Entity\Entity;
use AppOficina\Shared\Mapper\MapperInterface;
use Symfony\Component\Uid\Ulid;

class CarEloquentMapper implements MapperInterface
{
    public function toDomain(object $persistenceModel): Entity
    {
        $car = Car::create(
            brand: $persistenceModel->brand,
            model: $persistenceModel->model,
            year: $persistenceModel->year,
            typeCar: $persistenceModel->type,
            clientId: Ulid::fromString($persistenceModel->client_id),
            id: Ulid::fromString($persistenceModel->id)
        );

        if ($persistenceModel->licence_plate) {
            $car = $car->withLicencePlate($persistenceModel->licence_plate);
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

    /** @param Car $domainModel */
    public function toPersistence(Entity $domainModel): array
    {
        if (!$domainModel instanceof Car) {
            throw new \InvalidArgumentException('Invalid domain model type');
        }

        return [
            'id' => $domainModel->getId()->toString(),
            'client_id' => $domainModel->clientId->toString(),
            'brand' => $domainModel->brand,
            'model' => $domainModel->model,
            'year' => $domainModel->year,
            'type' => $domainModel->type->value,
            'licence_plate' => $domainModel->licencePlate ? $domainModel->licencePlate->value() : null,
            'vin' => $domainModel->vin ? $domainModel->vin->value() : null,
            'transmission' => $domainModel->transmission ? $domainModel->transmission->value : null,
            'color' => $domainModel->color ?: null,
            'cilinder_capacity' => $domainModel->cilinderCapacity ?: null,
            'mileage' => $domainModel->mileage ?: null,
            'observations' => $domainModel->observations ?: null,
        ];
    }
}
