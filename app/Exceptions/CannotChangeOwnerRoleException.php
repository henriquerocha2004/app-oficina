<?php

namespace App\Exceptions;

use Exception;

class CannotChangeOwnerRoleException extends Exception
{
    protected $message = 'A role do usuário Owner não pode ser alterada.';
}
