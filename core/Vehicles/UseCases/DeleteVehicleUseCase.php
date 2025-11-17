<?php

declare(strict_types=1);

namespace AppOficina\Vehicles\UseCases;

use AppOficina\Vehicles\Entities\Vehicle;
use AppOficina\Vehicles\Exceptions\VehicleNotFoundException;
use AppOficina\Vehicles\Repository\VehicleRepositoryInterface;
use AppOficina\Shared\Exceptions\NotFoundException;
use Symfony\Component\Uid\Ulid;

class DeleteVehicleUseCase
{
    public function __construct(
        private readonly VehicleRepositoryInterface $repository
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function execute(string $id): void
    {
        $id = Ulid::fromString($id);
        $car = $this->repository->findById($id);
        if (!$car) {
            throw new VehicleNotFoundException();
        }

        $this->repository->delete($id);
    }
}
