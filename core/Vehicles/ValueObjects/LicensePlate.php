<?php

namespace AppOficina\Vehicles\ValueObjects;

use AppOficina\Vehicles\Exceptions\InvalidLicensePlateException;

class LicensePlate
{
    public function __construct(
        private string $plate,
    ) {
        $this->plate = $this->formatPlate($this->plate);

        if (!$this->isValid()) {
            throw new InvalidLicensePlateException();
        }
    }

    public function isValid(): bool
    {
        return $this->isOldFormat() || $this->isMercosulFormat();
    }

    public function isMercosulFormat(): bool
    {
        return preg_match('/^[A-Z]{3}\d[A-Z]\d{2}$/', $this->plate) === 1;
    }

    public function getFormattedPlate(): string
    {
        if ($this->isOldFormat()) {
            return substr($this->plate, 0, 3) . '-' . substr($this->plate, 3);
        }

        if ($this->isMercosulFormat()) {
            return substr($this->plate, 0, 3) . substr($this->plate, 3, 1)  .
                substr($this->plate, 4, 1) . substr($this->plate, 5);
        }

        return $this->plate;
    }

    private function isOldFormat(): bool
    {
        return preg_match('/^[A-Z]{3}\d{4}$/', $this->plate) === 1;
    }

    private function formatPlate(string $plate): string
    {
        return strtoupper(str_replace(['-', ' '], '', $plate));
    }

    public function value(): string
    {
        return $this->plate;
    }
}
