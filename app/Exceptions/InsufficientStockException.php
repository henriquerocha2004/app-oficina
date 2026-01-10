<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(
        public readonly int $available,
        public readonly int $requested,
        string $message = "",
    ) {
        $message = $message ?: "Estoque insuficiente. Disponível: {$available}, Solicitado: {$requested}";
        parent::__construct($message);
    }
}
