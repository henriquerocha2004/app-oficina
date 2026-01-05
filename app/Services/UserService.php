<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use DomainException;
use App\DTOs\SearchDTO;
use App\DTOs\UserInputDTO;
use App\DTOs\UserOutputDTO;
use Illuminate\Support\Str;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * List users with pagination and filters.
     */
    public function list(SearchDTO $searchDTO): LengthAwarePaginator
    {
        $query = User::with(['role']);

        // Apply search
        if ($searchDTO->search) {
            $query->where(function ($q) use ($searchDTO) {
                $q->where('name', 'like', "%{$searchDTO->search}%")
                    ->orWhere('email', 'like', "%{$searchDTO->search}%");
            });
        }

        // Apply filters
        if (!empty($searchDTO->filters)) {
            if (isset($searchDTO->filters['role_id'])) {
                $query->where('role_id', $searchDTO->filters['role_id']);
            }

            if (isset($searchDTO->filters['is_owner'])) {
                $query->where('is_owner', $searchDTO->filters['is_owner']);
            }
        }

        // Apply sorting
        $query->orderBy(
            $searchDTO->sortBy ?? 'created_at',
            $searchDTO->sortOrder ?? 'desc'
        );

        return $query->paginate($searchDTO->perPage ?? 15);
    }

    /**
     * Update user.
     */
    public function update(string $id, UserInputDTO $dto): void
    {
        $user = User::findOrFail($id);

        $user->update($dto->toArray());
    }

    /**
     * Delete user.
     */
    public function delete(string $id): bool
    {
        $user = User::findOrFail($id);

        if ($user->is_owner) {
            throw new DomainException('Não é possível deletar o proprietário do tenant.');
        }

        return $user->delete();
    }

    /**
     * Change user role.
     */
    public function changeRole(string $userId, string $roleId): void
    {
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($roleId);

        if ($user->is_owner && $role->slug !== 'owner') {
            throw new DomainException('Não é possível alterar o perfil do proprietário.');
        }

        $user->update(['role_id' => $roleId]);
    }

    /**
     * Deactivate user (soft delete or status flag).
     */
    public function deactivate(string $id): void
    {
        $user = User::findOrFail($id);

        if ($user->is_owner) {
            throw new DomainException('Não é possível desativar o proprietário do tenant.');
        }

        // For now, we'll use deletion. Can be changed to status flag if needed
        $user->delete();
    }

    /**
     * Get user by ID with role and permissions.
     */
    public function find(string $id): ?UserOutputDTO
    {
        $user = User::with('role')->find($id);

        return $user ? UserOutputDTO::fromModel($user) : null;
    }

    /**
     * Validate plan limits before creating user.
     */
    public function validatePlanLimits(): void
    {
        $tenant = tenancy()->tenant;

        if (!$tenant) {
            return;
        }

        $currentUserCount = User::count();
        $maxUsers = $tenant->subscriptionPlan->limits['max_users'] ?? null;

        if ($maxUsers !== null && ($currentUserCount + 1) > $maxUsers) {
            throw new DomainException(
                "Limite de usuários atingido. Seu plano permite até {$maxUsers} usuários."
            );
        }
    }

    /**
     * Generate reset password token for user.
     */
    public function generatePasswordResetToken(string $userId): string
    {
        // This would integrate with Laravel's password reset functionality
        // For now, returning placeholder
        return Str::random(64);
    }
}
