<?php

namespace AppOficina\Cars\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidLicensePlateException extends HttpException
{
    public function __construct()
    {
        parent::__construct(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid license plate format.');
    }
}
