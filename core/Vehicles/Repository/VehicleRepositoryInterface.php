<?php

declare(strict_types=1);

namespace AppOficina\Vehicles\Repository;

use AppOficina\Vehicles\Entities\Vehicle;
use AppOficina\Shared\Repository\RepositoryInterface;
use Symfony\Component\Uid\Ulid;

interface VehicleRepositoryInterface extends RepositoryInterface
{
    public function findByLicensePlate(string $licensePlate): ?Vehicle;

    public function findByVin(string $vin): ?Vehicle;

    /**
     * @return array<Vehicle>
     */
    public function findByClientId(Ulid $clientId): array;
}
