<?php

namespace AppOficina\Cars\ValueObjects;

use AppOficina\Cars\Exceptions\InvalidChassiException;

class Vin
{
    public function __construct(
        private readonly string $vin,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (!preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', $this->vin)) {
            throw new InvalidChassiException();
        }
    }

    public function value(): string
    {
        return $this->vin;
    }

    public function equals(Vin $otherVin): bool
    {
        return $this->vin === $otherVin->vin;
    }
}
