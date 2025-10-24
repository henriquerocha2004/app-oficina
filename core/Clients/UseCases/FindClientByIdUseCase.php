<?php

declare(strict_types=1);

namespace AppOficina\Clients\UseCases;

use AppOficina\Clients\Exceptions\ClientNotFoundException;
use AppOficina\Clients\Repository\ClientRepositoryInterface;
use AppOficina\Shared\Exceptions\NotFoundException;
use Symfony\Component\Uid\Ulid;

class FindClientByIdUseCase
{
    public function __construct(
        private readonly ClientRepositoryInterface $repository
    ) {
    }

    /**
     * @return array<string, mixed>
     * @throws NotFoundException
     */
    public function execute(string $id): array
    {
        $client = $this->repository->findById(Ulid::fromString($id));

        if (!$client) {
            throw new ClientNotFoundException();
        }

        return $client->toArray();
    }
}
