<?php

namespace App\DTOs\Admin;

readonly class AdminLoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember = false
    ) {
    }

    /**
     * Create from request data.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            remember: $data['remember'] ?? false
        );
    }

    /**
     * Get credentials array for authentication.
     *
     * @return array<string, string>
     */
    public function credentials(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
