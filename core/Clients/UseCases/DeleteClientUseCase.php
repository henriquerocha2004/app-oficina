<?php

declare(strict_types=1);

namespace AppOficina\Clients\UseCases;

use AppOficina\Clients\Exceptions\ClientNotFoundException;
use AppOficina\Clients\Repository\ClientRepositoryInterface;
use AppOficina\Shared\Exceptions\NotFoundException;
use Symfony\Component\Uid\Ulid;

class DeleteClientUseCase
{
    public function __construct(
        private readonly ClientRepositoryInterface $repository
    ) {
    }

    /**
     * @throws ClientNotFoundException
     */
    public function execute(string $id): void
    {
        $id = Ulid::fromString($id);
        $client = $this->repository->findById($id);
        if (!$client) {
            throw new ClientNotFoundException();
        }

        $this->repository->delete($id);
    }
}
