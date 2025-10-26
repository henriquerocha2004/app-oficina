<?php

declare(strict_types=1);

namespace AppOficina\Cars\UseCases;

use AppOficina\Cars\Entities\Car;
use AppOficina\Cars\Exceptions\CarNotFoundException;
use AppOficina\Cars\Repository\CarRepositoryInterface;
use AppOficina\Shared\Exceptions\NotFoundException;
use Symfony\Component\Uid\Ulid;

class DeleteCarUseCase
{
    public function __construct(
        private readonly CarRepositoryInterface $repository
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
            throw new CarNotFoundException();
        }

        $this->repository->delete($id);
    }
}
