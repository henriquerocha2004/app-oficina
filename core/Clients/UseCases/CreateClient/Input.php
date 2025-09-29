<?php

namespace AppOficina\Clients\UseCases\CreateClient;

class Input
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $document,
        public readonly ?array $address = null,
        public readonly ?string $phone = null,
        public readonly ?string $observations = null,
    ) {
    }
}
