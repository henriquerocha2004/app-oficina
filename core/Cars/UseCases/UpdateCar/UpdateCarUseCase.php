<?php

declare(strict_types=1);

namespace AppOficina\Cars\UseCases\UpdateCar;

use AppOficina\Cars\Entities\Car;
use AppOficina\Cars\Exceptions\CarNotFoundException;
use AppOficina\Cars\Repository\CarRepositoryInterface;
use AppOficina\Shared\Exceptions\NotFoundException;
use Symfony\Component\Uid\Ulid;

class UpdateCarUseCase
{
    public function __construct(
        private readonly CarRepositoryInterface $repository
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function execute(Input $input): void
    {
        /** @var Car $car */
        $car = $this->repository->findById(Ulid::fromString($input->carId));

        if (!$car) {
            throw new CarNotFoundException();
        }

        $updatedCar = $this->applyUpdates($car, $input);
        $this->repository->update($updatedCar);
    }

    private function applyUpdates(Car $car, Input $input): Car
    {
        $updatedCar = $car;

        if ($input->brand !== null) {
            $updatedCar = $updatedCar->withBrand($input->brand);
        }

        if ($input->model !== null) {
            $updatedCar = $updatedCar->withModel($input->model);
        }

        if ($input->year !== null) {
            $updatedCar = $updatedCar->withYear($input->year);
        }

        if ($input->type !== null) {
            $updatedCar = $updatedCar->withType($input->type);
        }

        if ($input->licencePlate !== null) {
            $updatedCar = $updatedCar->withLicencePlate($input->licencePlate);
        }

        if ($input->vin !== null) {
            $updatedCar = $updatedCar->withVin($input->vin);
        }

        if ($input->transmission !== null) {
            $updatedCar = $updatedCar->withTransmission($input->transmission);
        }

        if ($input->color !== null) {
            $updatedCar = $updatedCar->withColor($input->color);
        }

        if ($input->cilinderCapacity !== null) {
            $updatedCar = $updatedCar->withCilinderCapacity($input->cilinderCapacity);
        }

        if ($input->mileage !== null) {
            $updatedCar = $updatedCar->withMileage($input->mileage);
        }

        if ($input->observations !== null) {
            $updatedCar = $updatedCar->withObservations($input->observations);
        }

        return $updatedCar;
    }
}
