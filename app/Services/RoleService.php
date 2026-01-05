<?php

namespace App\Services;

use App\DTOs\RoleInputDTO;
use App\DTOs\SearchDTO;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleService
{
    /**
     * List roles with pagination.
     */
    public function list(SearchDTO $searchDTO): LengthAwarePaginator
    {
        $query = Role::withCount('users');

        if ($searchDTO->search) {
            $query->where(function ($q) use ($searchDTO) {
                $q->where('name', 'like', "%{$searchDTO->search}%")
                    ->orWhere('slug', 'like', "%{$searchDTO->search}%");
            });
        }

        $query->orderBy($searchDTO->sortBy ?? 'name', $searchDTO->sortOrder ?? 'asc');

        return $query->paginate($searchDTO->perPage ?? 15);
    }

    /**
     * Get all roles for dropdown.
     */
    public function getAll()
    {
        return Role::orderBy('name')->get();
    }

    /**
     * Create a new role.
     */
    public function create(RoleInputDTO $dto): Role
    {
        return Role::create($dto->toCreateArray());
    }

    /**
     * Update role.
     */
    public function update(string $id, RoleInputDTO $dto): void
    {
        $role = Role::findOrFail($id);

        $this->validateSystemRole($role);

        $role->update($dto->toArray());
    }

    /**
     * Delete role.
     */
    public function delete(string $id): bool
    {
        $role = Role::findOrFail($id);

        $this->validateSystemRole($role);

        if ($role->users()->count() > 0) {
            throw new \DomainException(
                'Não é possível deletar um perfil que possui usuários vinculados. ' .
                'Reassine os usuários para outro perfil primeiro.'
            );
        }

        return $role->delete();
    }

    /**
     * Find role by ID with permissions.
     */
    public function find(string $id): ?Role
    {
        return Role::with('permissions')->find($id);
    }

    /**
     * Sync permissions for a role.
     */
    public function syncPermissions(string $roleId, array $permissionIds): void
    {
        $role = Role::findOrFail($roleId);

        // System roles can have permissions managed
        // but we could add validation here if needed

        $role->permissions()->sync($permissionIds);
    }

    /**
     * Get all permissions grouped by module.
     */
    public function getPermissionsGroupedByModule(): array
    {
        return Permission::orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module')
            ->toArray();
    }

    /**
     * Validate that role is not a system role (for deletion/modification).
     */
    private function validateSystemRole(Role $role): void
    {
        if ($role->is_system) {
            throw new \DomainException('Não é possível modificar ou deletar perfis do sistema.');
        }
    }
}
