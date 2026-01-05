<?php

namespace App\Exceptions;

use Exception;

class InvitationExpiredException extends Exception
{
    protected $message = 'Este convite expirou. Solicite um novo convite ao administrador.';
}
