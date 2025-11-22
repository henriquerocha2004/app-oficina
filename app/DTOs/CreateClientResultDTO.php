<?php

namespace App\DTOs;

readonly class CreateClientResultDTO
{
    public function __construct(
        public ClientOutputDTO $client,
        public bool $created
    ) {
    }
}
