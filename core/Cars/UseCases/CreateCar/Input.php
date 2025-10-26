<?php

namespace AppOficina\Cars\UseCases\CreateCar;

class Input
{
    public function __construct(
        public readonly string $brand,
        public readonly string $model,
        public readonly int $year,
        public readonly string $type,
        public readonly string $clientId,
        public readonly ?string $licencePlate = null,
        public readonly ?string $vin = null,
        public readonly ?string $transmission = null,
        public readonly ?string $color = null,
        public readonly ?int $cilinderCapacity = null,
        public readonly ?int $mileage = null,
        public readonly ?string $observations = null,
    ) {
    }
}
