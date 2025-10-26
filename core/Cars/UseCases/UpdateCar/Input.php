<?php

namespace AppOficina\Cars\UseCases\UpdateCar;

class Input
{
    public function __construct(
        public readonly string $carId,
        public readonly ?string $clientId = null,
        public readonly ?string $brand = null,
        public readonly ?string $model = null,
        public readonly ?int $year = null,
        public readonly ?string $type = null,
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
