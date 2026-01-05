<?php

namespace App\Exceptions;

use Exception;

class InvitationAlreadyAcceptedException extends Exception
{
    protected $message = 'Este convite já foi aceito anteriormente.';
}
