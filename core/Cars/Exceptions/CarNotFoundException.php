<?php

declare(strict_types=1);

namespace AppOficina\Cars\Exceptions;

use AppOficina\Shared\Exceptions\NotFoundException;

class CarNotFoundException extends NotFoundException
{
    public function __construct()
    {
        parent::__construct('Car not found.');
    }
}
