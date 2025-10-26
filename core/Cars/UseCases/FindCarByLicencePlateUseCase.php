<?php

declare(strict_types=1);

namespace AppOficina\Cars\UseCases;

use AppOficina\Cars\Exceptions\CarNotFoundException;
use AppOficina\Cars\Repository\CarRepositoryInterface;
use AppOficina\Shared\Exceptions\NotFoundException;

class FindCarByLicencePlateUseCase
{
    public function __construct(
        private readonly CarRepositoryInterface $repository
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
            throw new CarNotFoundException();
        }

        return $car->toArray();
    }
}
