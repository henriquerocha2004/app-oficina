<?php

declare(strict_types=1);

namespace AppOficina\Vehicles\UseCases;

use AppOficina\Vehicles\Exceptions\VehicleNotFoundException;
use AppOficina\Vehicles\Repository\VehicleRepositoryInterface;
use AppOficina\Shared\Exceptions\NotFoundException;

class FindVehicleByVinUseCase
{
    public function __construct(
        private readonly VehicleRepositoryInterface $repository
    ) {
    }

    /**
     * @return array<string, mixed>
     * @throws NotFoundException
     */
    public function execute(string $vin): array
    {
        $car = $this->repository->findByVin($vin);

        if (!$car) {
            throw new VehicleNotFoundException();
        }

        return $car->toArray();
    }
}
