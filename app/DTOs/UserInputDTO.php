<?php

namespace App\DTOs;

readonly class UserInputDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $roleId = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            roleId: $data['role_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->roleId,
        ], fn($value) => $value !== null);
    }
}
