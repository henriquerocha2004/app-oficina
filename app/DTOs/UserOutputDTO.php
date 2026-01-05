<?php

namespace App\DTOs;

use App\Models\User;

readonly class UserOutputDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $emailVerifiedAt,
        public ?string $roleId,
        public ?string $roleName,
        public bool $isOwner,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            emailVerifiedAt: $user->email_verified_at?->toISOString(),
            roleId: $user->role_id,
            roleName: $user->role?->name,
            isOwner: $user->is_owner,
            createdAt: $user->created_at->toISOString(),
            updatedAt: $user->updated_at->toISOString(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->emailVerifiedAt,
            'role_id' => $this->roleId,
            'role_name' => $this->roleName,
            'is_owner' => $this->isOwner,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
