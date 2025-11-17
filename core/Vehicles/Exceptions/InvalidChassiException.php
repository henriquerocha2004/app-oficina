<?php

namespace AppOficina\Vehicles\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidChassiException extends HttpException
{
    public function __construct()
    {
        parent::__construct(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid chassis number format.');
    }
}
