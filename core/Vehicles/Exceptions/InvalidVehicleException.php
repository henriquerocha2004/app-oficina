<?php

namespace AppOficina\Vehicles\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidVehicleException extends HttpException
{
    public function __construct(string $message = 'Invalid vehicle data.')
    {
        parent::__construct(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
    }
}
