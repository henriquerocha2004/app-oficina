<?php

namespace AppOficina\Clients\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClientNotFoundException extends HttpException
{
    public function __construct()
    {
        parent::__construct(Response::HTTP_NOT_FOUND, 'Client not found.');
    }
}
