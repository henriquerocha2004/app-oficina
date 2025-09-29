<?php

declare(strict_types=1);

namespace AppOficina\Clients\ValueObjects;

use InvalidArgumentException;

final class Phone
{
    public function __construct(
        public string $number
    ) {
        $this->number = preg_replace('/\D/', '', $this->number);

        if (!preg_match('/^\d{10,11}$/', $this->number)) {
            throw new InvalidArgumentException('Invalid phone number.');
        }
    }

    public function __toString(): string
    {
        return $this->number;
    }

    public function toArray(): array
    {
        return [
            'number' => $this->number,
        ];
    }
}
