<?php

declare(strict_types=1);

namespace AppOficina\Cars\UseCases;

use AppOficina\Cars\Exceptions\CarNotFoundException;
use AppOficina\Cars\Repository\CarRepositoryInterface;
use AppOficina\Shared\Exceptions\NotFoundException;

class FindCarByVinUseCase
{
    public function __construct(
        private readonly CarRepositoryInterface $repository
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
            throw new CarNotFoundException();
        }

        return $car->toArray();
    }
}
