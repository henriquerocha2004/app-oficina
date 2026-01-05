<?php

namespace App\Exceptions;

use Exception;

class CannotDeleteSystemRoleException extends Exception
{
    protected $message = 'Roles do sistema não podem ser excluídas.';
}
