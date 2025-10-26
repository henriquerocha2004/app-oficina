<?php

declare(strict_types=1);

namespace AppOficina\Cars\Repository;

use AppOficina\Cars\Entities\Car;
use AppOficina\Shared\Repository\RepositoryInterface;
use Symfony\Component\Uid\Ulid;

interface CarRepositoryInterface extends RepositoryInterface
{
    public function findByLicencePlate(string $licencePlate): ?Car;

    public function findByVin(string $vin): ?Car;

    /**
     * @return array<Car>
     */
    public function findByClientId(Ulid $clientId): array;
}
