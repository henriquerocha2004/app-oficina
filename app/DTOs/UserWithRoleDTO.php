<?php

namespace App\DTOs;

use App\Models\User;

readonly class UserWithRoleDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $roleId,
        public ?array $role,
        public bool $isOwner,
        public array $permissions,
    ) {
    }

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            roleId: $user->role_id,
            role: $user->role ? [
                'id' => $user->role->id,
                'name' => $user->role->name,
                'slug' => $user->role->slug,
                'is_system' => $user->role->is_system,
            ] : null,
            isOwner: $user->is_owner,
            permissions: $user->getPermissionSlugs(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->roleId,
            'role' => $this->role,
            'is_owner' => $this->isOwner,
            'permissions' => $this->permissions,
        ];
    }
}
