<?php

namespace App\Services;

use App\DTOs\SearchDTO;
use App\DTOs\SupplierInputDTO;
use App\DTOs\SupplierOutputDTO;
use App\Models\Supplier;
use App\Services\Traits\QueryBuilderTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierService
{
    use QueryBuilderTrait;

    /**
     * Create a new supplier
     */
    public function create(SupplierInputDTO $dto): SupplierOutputDTO
    {
        $supplier = Supplier::create($dto->toArray());

        return SupplierOutputDTO::fromModel($supplier);
    }

    /**
     * Update an existing supplier
     */
    public function update(string $id, SupplierInputDTO $dto): void
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update($dto->toArray());
    }

    /**
     * Delete a supplier (soft delete)
     */
    public function delete(string $id): bool
    {
        $supplier = Supplier::findOrFail($id);
        return $supplier->delete();
    }

    /**
     * Find a supplier by ID
     */
    public function find(string $id): ?SupplierOutputDTO
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return null;
        }

        return SupplierOutputDTO::fromModel($supplier);
    }

    /**
     * List suppliers with search and filters
     */
    public function list(SearchDTO $searchDTO): LengthAwarePaginator
    {
        $query = Supplier::query();

        $searchableColumns = ['name', 'trade_name', 'document_number', 'email', 'city'];

        return $this->applySearchAndFilters($query, $searchDTO, $searchableColumns);
    }
}
