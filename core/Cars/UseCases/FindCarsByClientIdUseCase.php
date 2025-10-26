<?php

declare(strict_types=1);

namespace AppOficina\Cars\UseCases;

use AppOficina\Cars\Repository\CarRepositoryInterface;
use Symfony\Component\Uid\Ulid;

class FindCarsByClientIdUseCase
{
    public function __construct(
        private readonly CarRepositoryInterface $repository
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
