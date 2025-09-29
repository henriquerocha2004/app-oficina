<?php

namespace AppOficina\Shared\Repository;

use AppOficina\Shared\Entity\Entity;
use AppOficina\Shared\Search\SearchRequest;
use AppOficina\Shared\Search\SearchResponse;
use Symfony\Component\Uid\Ulid;

interface RepositoryInterface
{
    public function save(Entity $entity): void;
    public function update(Entity $entity): void;
    public function delete(Ulid $id): void;
    public function findById(Ulid $id): ?Entity;
    public function findAll(SearchRequest $search): SearchResponse;
}
