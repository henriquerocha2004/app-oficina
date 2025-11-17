<?php

declare(strict_types=1);

namespace AppOficina\Vehicles\UseCases;

use AppOficina\Vehicles\Exceptions\VehicleNotFoundException;
use AppOficina\Vehicles\Repository\VehicleRepositoryInterface;
use AppOficina\Shared\Exceptions\NotFoundException;
use Symfony\Component\Uid\Ulid;

class FindVehicleByIdUseCase
{
    public function __construct(
        private readonly VehicleRepositoryInterface $repository
    ) {
    }

    /**
     * @return array<string, mixed>
     * @throws NotFoundException
     */
    public function execute(string $id): array
    {
        $car = $this->repository->findById(Ulid::fromString($id));

        if (!$car) {
            throw new VehicleNotFoundException();
        }

        return $car->toArray();
    }
}
