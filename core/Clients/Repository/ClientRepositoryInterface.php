<?php

declare(strict_types=1);

namespace AppOficina\Clients\Repository;

use AppOficina\Clients\Entities\Client;
use AppOficina\Shared\Repository\RepositoryInterface;

interface ClientRepositoryInterface extends RepositoryInterface
{
    public function findByDocument(string $document): ?Client;
}
