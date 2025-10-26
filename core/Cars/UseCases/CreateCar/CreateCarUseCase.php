<?php

declare(strict_types=1);

namespace AppOficina\Cars\UseCases\CreateCar;

use AppOficina\Cars\Entities\Car;
use AppOficina\Cars\Repository\CarRepositoryInterface;

class CreateCarUseCase
{
    public function __construct(
        private readonly CarRepositoryInterface $repository
    ) {
    }

    public function execute(Input $data): Output
    {
        $existingCar = $this->findExistingCar($data);
        if ($existingCar !== null) {
            return new Output(carId: $existingCar->getId()->__toString());
        }

        $car = $this->createNewCar($data);
        $this->repository->save($car);

        return new Output(carId: $car->getId()->__toString());
    }

    private function findExistingCar(Input $data): ?Car
    {
        if ($data->licencePlate !== null) {
            $existingCar = $this->repository->findByLicencePlate($data->licencePlate);
            if ($existingCar !== null) {
                return $existingCar;
            }
        }

        if ($data->vin !== null) {
            $existingCar = $this->repository->findByVin($data->vin);
            if ($existingCar !== null) {
                return $existingCar;
            }
        }

        return null;
    }

    private function createNewCar(Input $data): Car
    {
        $car = Car::create(
            brand: $data->brand,
            model: $data->model,
            year: $data->year,
            typeCar: $data->type,
            clientId: \Symfony\Component\Uid\Ulid::fromString($data->clientId),
        );

        return $this->applyOptionalAttributes($car, $data);
    }

    private function applyOptionalAttributes(Car $car, Input $data): Car
    {
        if ($data->licencePlate !== null) {
            $car = $car->withLicencePlate($data->licencePlate);
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
