<?php

namespace AppOficina\Cars\UseCases\CreateCar;

class Output
{
    public function __construct(
        public readonly string $carId,
    ) {
    }
}
