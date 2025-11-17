<?php

declare(strict_types=1);

namespace AppOficina\Vehicles\Exceptions;

use AppOficina\Shared\Exceptions\NotFoundException;

class VehicleNotFoundException extends NotFoundException
{
    public function __construct()
    {
        parent::__construct('Car not found.');
    }
}
