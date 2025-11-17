<?php

declare(strict_types=1);

namespace AppOficina\Vehicles\UseCases;

use AppOficina\Vehicles\Repository\VehicleRepositoryInterface;
use Symfony\Component\Uid\Ulid;

class FindVehiclesByClientIdUseCase
{
    public function __construct(
        private readonly VehicleRepositoryInterface $repository
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function execute(string $clientId): array
    {
        $cars = $this->repository->findByClientId(Ulid::fromString($clientId));

        return array_map(
            fn($car) => $car->toArray(),
            $cars
        );
    }
}
