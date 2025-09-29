<?php

declare(strict_types=1);

namespace AppOficina\Clients\UseCases;

use AppOficina\Clients\Repository\ClientRepositoryInterface;
use AppOficina\Shared\Search\SearchRequest;
use AppOficina\Shared\Search\SearchResponse;

class ListClientsUseCase
{
    public function __construct(
        private readonly ClientRepositoryInterface $repository
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function execute(SearchRequest $searchRequest): SearchResponse
    {
       return $this->repository->findAll($searchRequest);
    }
}
