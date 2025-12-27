<?php

namespace App\Services\Admin;

use App\DTOs\Admin\SubscriptionPlanInputDTO;
use App\DTOs\SearchDTO;
use App\Models\SubscriptionPlan;
use App\Services\Traits\QueryBuilderTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SubscriptionPlanService
{
    use QueryBuilderTrait;

    public function list(SearchDTO $searchDTO): LengthAwarePaginator
    {
        $query = SubscriptionPlan::query();

        return $this->applySearchAndFilters($query, $searchDTO, ['name', 'slug']);
    }

    public function create(SubscriptionPlanInputDTO $dto): SubscriptionPlan
    {
        return SubscriptionPlan::create($dto->toArray());
    }

    public function update(string $id, SubscriptionPlanInputDTO $dto): void
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $plan->update($dto->toArray());
    }

    public function delete(string $id): void
    {
        $plan = SubscriptionPlan::findOrFail($id);
        if ($plan->tenants()->count() > 0) {
            throw new \DomainException('Não é possível excluir um plano que possui oficinas associadas.');
        }

        $plan->delete();
    }
}
