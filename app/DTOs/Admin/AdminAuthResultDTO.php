<?php

namespace App\DTOs\Admin;

use App\Models\AdminUser;

readonly class AdminAuthResultDTO
{
    public function __construct(
        public bool $success,
        public ?AdminUser $user = null,
        public ?string $errorMessage = null
    ) {
    }

    /**
     * Create a successful authentication result.
     */
    public static function success(AdminUser $user): self
    {
        return new self(
            success: true,
            user: $user
        );
    }

    /**
     * Create a failed authentication result.
     */
    public static function failed(string $errorMessage = 'Credenciais inválidas'): self
    {
        return new self(
            success: false,
            errorMessage: $errorMessage
        );
    }

    /**
     * Check if authentication was successful.
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Check if authentication failed.
     */
    public function isFailed(): bool
    {
        return !$this->success;
    }
}
