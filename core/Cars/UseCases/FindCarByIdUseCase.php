<?php

declare(strict_types=1);

namespace AppOficina\Cars\UseCases;

use AppOficina\Cars\Exceptions\CarNotFoundException;
use AppOficina\Cars\Repository\CarRepositoryInterface;
use AppOficina\Shared\Exceptions\NotFoundException;
use Symfony\Component\Uid\Ulid;

class FindCarByIdUseCase
{
    public function __construct(
        private readonly CarRepositoryInterface $repository
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
            throw new CarNotFoundException();
        }

        return $car->toArray();
    }
}
