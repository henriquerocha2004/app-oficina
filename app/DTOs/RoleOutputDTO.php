<?php

namespace App\DTOs;

use App\Models\Role;

readonly class RoleOutputDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public ?string $description,
        public bool $isSystem,
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
            'users_count' => $this->usersCount,
        ];
    }
}
