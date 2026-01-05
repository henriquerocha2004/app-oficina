<?php

namespace App\DTOs;

use App\Models\Role;

readonly class RoleWithPermissionsDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public ?string $description,
        public bool $isSystem,
        public array $permissions,
        public int $usersCount,
    ) {
    }

    public static function fromModel(Role $role): self
    {
        return new self(
            id: $role->id,
            name: $role->name,
            slug: $role->slug,
            description: $role->description,
            isSystem: $role->is_system,
            permissions: $role->permissions->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'module' => $p->module,
            ])->toArray(),
            usersCount: $role->users()->count(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_system' => $this->isSystem,
            'permissions' => $this->permissions,
            'users_count' => $this->usersCount,
        ];
    }
}
