<?php

namespace AppOficina\Clients\UseCases\UpdateClient;

class Input
{
    public function __construct(
        public readonly string $clientId,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $document = null,
        public readonly ?array $address = null,
        public readonly ?string $phone = null,
        public readonly ?string $observations = null,
    ) {
    }
}
