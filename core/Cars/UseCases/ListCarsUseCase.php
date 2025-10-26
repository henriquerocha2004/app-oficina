<?php

declare(strict_types=1);

namespace AppOficina\Cars\UseCases;

use AppOficina\Cars\Repository\CarRepositoryInterface;
use AppOficina\Shared\Search\SearchRequest;
use AppOficina\Shared\Search\SearchResponse;

class ListCarsUseCase
{
    public function __construct(
        private readonly CarRepositoryInterface $repository
    ) {
    }

    /**
     * @return SearchResponse
     */
    public function execute(SearchRequest $searchRequest): SearchResponse
    {
        return $this->repository->findAll($searchRequest);
    }
}
