<?php

namespace App\Models\Concerns;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

trait HasRoleAndPermissions
{
    /**
     * Get the role of the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string|array $roleSlug): bool
    {
        if (!$this->role) {
            return false;
        }

        $slugs = is_array($roleSlug) ? $roleSlug : [$roleSlug];

        return in_array($this->role->slug, $slugs);
    }

    /**
     * Check if user is the owner of the tenant.
     */
    public function isOwner(): bool
    {
        return (bool) $this->is_owner;
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasPermission($permissionSlug);
    }

    /**
     * Check if user does not have a specific permission.
     */
    public function lacksPermission(string $permissionSlug): bool
    {
        return !$this->hasPermission($permissionSlug);
    }

    /**
     * Get the role name of the user.
     */
    public function getRoleName(): string
    {
        return $this->role?->name ?? 'Sem Perfil';
    }

    /**
     * Get all permissions of the user through their role.
     */
    public function getPermissions(): Collection
    {
        if (!$this->role) {
            return collect([]);
        }

        return $this->role->permissions;
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }

        $this->update(['role_id' => $role->id]);
    }

    /**
     * Remove the role from the user.
     */
    public function removeRole(): void
    {
        $this->update(['role_id' => null]);
    }

    /**
     * Get permission slugs as array (useful for frontend).
     */
    public function getPermissionSlugs(): array
    {
        return $this->getPermissions()->pluck('slug')->toArray();
    }
}
