<?php

declare(strict_types=1);

namespace AppOficina\Vehicles\UseCases;

use AppOficina\Vehicles\Repository\VehicleRepositoryInterface;
use AppOficina\Shared\Search\SearchRequest;
use AppOficina\Shared\Search\SearchResponse;

class ListVehiclesUseCase
{
    public function __construct(
        private readonly VehicleRepositoryInterface $repository
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
