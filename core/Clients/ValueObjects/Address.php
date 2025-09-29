<?php

declare(strict_types=1);

namespace AppOficina\Clients\ValueObjects;

use InvalidArgumentException;

final class Address
{
    public function __construct(
        public readonly ?string $street,
        public readonly ?string $city,
        public readonly ?string $state,
        public readonly ?string $zipCode,
    ) {
        if (trim($street) === '' || trim($city) === '' || trim($state) === '' || trim($zipCode) === '') {
            throw new InvalidArgumentException('Address fields cannot be empty.');
        }
    }

    public function __toString(): string
    {
        return "{$this->street}, {$this->city} - {$this->state}, {$this->zipCode}";
    }

    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'city' => $this->city,
            'state' => $this->state,
            'zipCode' => $this->zipCode,
        ];
    }
}
