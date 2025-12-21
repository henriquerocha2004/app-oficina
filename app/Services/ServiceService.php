<?php

namespace App\Services;

use App\DTOs\ServiceInputDTO;
use App\DTOs\ServiceOutputDTO;
use App\DTOs\SearchDTO;
use App\Models\Service;
use App\Services\Traits\QueryBuilderTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ServiceService
{
    use QueryBuilderTrait;

    public function create(ServiceInputDTO $dto): ServiceOutputDTO
    {
        $service = Service::create($dto->toArray());

        return ServiceOutputDTO::fromModel($service);
    }

    public function update(string $id, ServiceInputDTO $dto): void
    {
        $service = Service::findOrFail($id);
        $service->update($dto->toArray());
    }

    public function delete(string $id): bool
    {
        $service = Service::find($id);

        if (!$service) {
            return false;
        }

        return $service->delete();
    }

    public function find(string $id): ?Service
    {
        return Service::find($id);
    }

    public function findByName(string $name): ?Service
    {
        return Service::where('name', 'like', "%{$name}%")->first();
    }

    public function findByCategory(string $category)
    {
        return Service::active()->byCategory($category)->get();
    }

    public function listActive(SearchDTO $searchDTO): LengthAwarePaginator
    {
        return $this->list($searchDTO, true);
    }

    public function list(SearchDTO $searchDTO, bool $onlyActive = false): LengthAwarePaginator
    {
        $query = Service::query();

        // Filter only active services
        if ($onlyActive) {
            $query->active();
        }

        // Search by name or description
        if (!empty($searchDTO->search)) {
            $search = $searchDTO->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if (!empty($searchDTO->filters['category'])) {
            $query->where('category', $searchDTO->filters['category']);
        }

        // Filter by is_active status
        if (isset($searchDTO->filters['is_active'])) {
            $query->where('is_active', (bool) $searchDTO->filters['is_active']);
        }

        // Filter by price range
        if (!empty($searchDTO->filters['min_price'])) {
            $query->where('base_price', '>=', $searchDTO->filters['min_price']);
        }

        if (!empty($searchDTO->filters['max_price'])) {
            $query->where('base_price', '<=', $searchDTO->filters['max_price']);
        }

        // Apply sorting
        $query->orderBy(
            $searchDTO->sort_by ?? 'name',
            $searchDTO->sort_direction ?? 'asc'
        );

        return $query->paginate($searchDTO->per_page ?? 10);
    }

    public function toggleActive(string $id): Service
    {
        $service = Service::findOrFail($id);
        $service->update(['is_active' => !$service->is_active]);

        return $service->fresh();
    }
}
