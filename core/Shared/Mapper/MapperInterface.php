<?php

namespace AppOficina\Shared\Mapper;

use AppOficina\Shared\Entity\Entity;

interface MapperInterface
{
    public function toDomain(object $persistenceModel): Entity;
    public function toPersistence(Entity $domainModel): array;
}
