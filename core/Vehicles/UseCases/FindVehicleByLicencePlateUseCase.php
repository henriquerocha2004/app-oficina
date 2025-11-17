<?php

declare(strict_types=1);

namespace AppOficina\Vehicles\UseCases;

use AppOficina\Vehicles\Exceptions\VehicleNotFoundException;
use AppOficina\Vehicles\Repository\VehicleRepositoryInterface;
use AppOficina\Shared\Exceptions\NotFoundException;

class FindVehicleByLicencePlateUseCase
{
    public function __construct(
        private readonly VehicleRepositoryInterface $repository
    ) {
    }

    /**
     * @return array<string, mixed>
     * @throws NotFoundException
     */
    public function execute(string $licencePlate): array
    {
        $car = $this->repository->findByLicencePlate($licencePlate);

        if (!$car) {
            throw new VehicleNotFoundException();
        }

        return $car->toArray();
    }
}
