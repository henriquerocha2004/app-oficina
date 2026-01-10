<?php

namespace App\DTOs;

use App\Models\Client;

readonly class ClientOutputDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public string $document_number,
        public ?string $phone = null,
        public ?string $street = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $zip_code = null,
    ) {
    }

    public static function fromModel(Client $client): self
    {
        return new self(
            id: $client->id,
            name: $client->name,
            email: $client->email,
            document_number: $client->document_number,
            phone: $client->phone,
            street: $client->street,
            city: $client->city,
            state: $client->state,
            zip_code: $client->zip_code,
        );
    }
}
