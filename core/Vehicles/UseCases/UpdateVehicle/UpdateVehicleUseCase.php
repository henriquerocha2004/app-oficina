<?php

declare(strict_types=1);

namespace AppOficina\Vehicles\UseCases\UpdateVehicle;

use AppOficina\Vehicles\Entities\Vehicle;
use AppOficina\Vehicles\Exceptions\VehicleNotFoundException;
use AppOficina\Vehicles\Repository\VehicleRepositoryInterface;
use AppOficina\Shared\Exceptions\NotFoundException;
use Symfony\Component\Uid\Ulid;

class UpdateVehicleUseCase
{
    public function __construct(
        private readonly VehicleRepositoryInterface $repository
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function execute(Input $input): void
    {
        /** @var Vehicle $car */
        $car = $this->repository->findById(Ulid::fromString($input->carId));

        if (!$car) {
            throw new VehicleNotFoundException();
        }

        $updatedVehicle = $this->applyUpdates($car, $input);
        $this->repository->update($updatedVehicle);
    }

    private function applyUpdates(Vehicle $car, Input $input): Vehicle
    {
        $updatedVehicle = $car;

        if ($input->brand !== null) {
            $updatedVehicle = $updatedVehicle->withBrand($input->brand);
        }

        if ($input->model !== null) {
            $updatedVehicle = $updatedVehicle->withModel($input->model);
        }

        if ($input->year !== null) {
            $updatedVehicle = $updatedVehicle->withYear($input->year);
        }

        if ($input->type !== null) {
            $updatedVehicle = $updatedVehicle->withType($input->type);
        }

        if ($input->licencePlate !== null) {
            $updatedVehicle = $updatedVehicle->withLicencePlate($input->licencePlate);
        }

        if ($input->vin !== null) {
            $updatedVehicle = $updatedVehicle->withVin($input->vin);
        }

        if ($input->transmission !== null) {
            $updatedVehicle = $updatedVehicle->withTransmission($input->transmission);
        }

        if ($input->color !== null) {
            $updatedVehicle = $updatedVehicle->withColor($input->color);
        }

        if ($input->cilinderCapacity !== null) {
            $updatedVehicle = $updatedVehicle->withCilinderCapacity($input->cilinderCapacity);
        }

        if ($input->mileage !== null) {
            $updatedVehicle = $updatedVehicle->withMileage($input->mileage);
        }

        if ($input->observations !== null) {
            $updatedVehicle = $updatedVehicle->withObservations($input->observations);
        }

        return $updatedVehicle;
    }
}
