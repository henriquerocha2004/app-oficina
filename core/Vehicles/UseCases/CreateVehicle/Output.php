<?php

namespace AppOficina\Vehicles\UseCases\CreateVehicle;

class Output
{
    public function __construct(
        public readonly string $carId,
    ) {
    }
}
