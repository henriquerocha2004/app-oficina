<?php

namespace App\Exceptions;

use Exception;

class PlanLimitExceededException extends Exception
{
    protected $message = 'Limite de usuários do plano atingido. Faça upgrade do seu plano.';
}
