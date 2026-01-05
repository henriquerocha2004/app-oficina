<?php

namespace App\Exceptions;

use Exception;

class CannotDeleteOwnerException extends Exception
{
    protected $message = 'O usuário Owner não pode ser excluído.';
}
