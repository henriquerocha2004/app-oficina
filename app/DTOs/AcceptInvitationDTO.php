<?php

namespace App\DTOs;

readonly class AcceptInvitationDTO
{
    public function __construct(
        public string $token,
        public string $name,
        public string $password,
    ) {
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            token: $data['token'],
            name: $data['name'],
            password: $data['password'],
        );
    }

    /**
     * Convert to array for user creation.
     */
    public function toUserArray(string $email, string $roleId): array
    {
        return [
            'name' => $this->name,
            'email' => $email,
            'password' => $this->password,
            'role_id' => $roleId,
            'email_verified_at' => now(),
        ];
    }
}
