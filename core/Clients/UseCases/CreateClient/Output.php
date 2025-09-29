<?php

namespace AppOficina\Clients\UseCases\CreateClient;

class Output
{
    public function __construct(
        public readonly string $clientId,
    ) {
    }
}
