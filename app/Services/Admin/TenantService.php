<?php

namespace App\Services\Admin;

use App\DTOs\Admin\TenantInputDTO;
use App\DTOs\Admin\TenantUpdateDTO;
use App\DTOs\SearchDTO;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Traits\QueryBuilderTrait;
use Database\Seeders\RoleSeeder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class TenantService
{
    use QueryBuilderTrait;

    public function list(SearchDTO $searchDTO): LengthAwarePaginator
    {
        $query = Tenant::with(['subscriptionPlan', 'domains']);

        return $this->applySearchAndFilters($query, $searchDTO, ['name', 'slug']);
    }

    public function getActiveSubscriptionPlans(): Collection
    {
        return SubscriptionPlan::where('is_active', true)
            ->orderBy('price_monthly')
            ->get();
    }

    public function create(TenantInputDTO $dto): Tenant
    {
        // Criar tenant no banco central
        // No ambiente de teste, criar sem eventos para evitar criação de banco físico
        $tenant = app()->environment('testing')
            ? Tenant::withoutEvents(fn() => Tenant::create($dto->getTenantData()))
            : Tenant::create($dto->getTenantData());

        // Criar domínio
        $tenant->domains()->create([
            'domain' => $dto->getDomainName(),
        ]);

        // Em ambiente de teste, não criar usuário admin (banco não existe)
        // Em produção, o banco é criado automaticamente pelo evento TenantCreated
        if (!app()->environment('testing')) {
            // Inicializar tenant para criar usuário admin
            tenancy()->initialize($tenant);

            try {
                // Seed roles and permissions
                RoleSeeder::seedForTenant();

                // Get Owner role
                $ownerRole = Role::where('slug', 'owner')->first();

                // Criar usuário admin no banco do tenant com role Owner
                User::create([
                    'name' => $dto->adminName,
                    'email' => $dto->adminEmail,
                    'password' => Hash::make($dto->adminPassword),
                    'email_verified_at' => now(),
                    'role_id' => $ownerRole?->id,
                    'is_owner' => true,
                ]);
            } finally {
                // Garantir que o contexto do tenant seja finalizado
                tenancy()->end();
            }
        }

        return $tenant;
    }

    public function update(string $id, TenantUpdateDTO $dto): void
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update($dto->toArray());
    }

    public function delete(string $id): void
    {
        $tenant = Tenant::findOrFail($id);

        // Em ambiente de teste, deletar sem eventos para evitar tentar dropar banco que não existe
        if (app()->environment('testing')) {
            $tenant->deleteQuietly();
        } else {
            $tenant->delete();
        }
    }
}
