<?php

namespace App\Services\Admin;

use App\DTOs\Admin\TenantInputDTO;
use App\DTOs\Admin\TenantUpdateDTO;
use App\DTOs\SearchDTO;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Traits\QueryBuilderTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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
        $tenant = Tenant::create($dto->getTenantData());

        // Criar domínio
        $tenant->domains()->create([
            'domain' => $dto->getDomainName(),
        ]);

        // Inicializar tenant para criar usuário admin
        tenancy()->initialize($tenant);

        try {
            // Criar usuário admin no banco do tenant
            User::create([
                'name' => $dto->adminName,
                'email' => $dto->adminEmail,
                'password' => Hash::make($dto->adminPassword),
                'email_verified_at' => now(),
            ]);
        } finally {
            // Garantir que o contexto do tenant seja finalizado
            tenancy()->end();
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
        $tenant->delete();
    }
}
