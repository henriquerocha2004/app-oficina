<?php

namespace AppOficina\Cars\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidCarException extends HttpException
{
    public function __construct(string $message)
    {
        parent::__construct(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
    }
}
