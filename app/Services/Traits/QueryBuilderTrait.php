<?php

namespace App\Services\Traits;

use App\DTOs\SearchDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait QueryBuilderTrait
{
    /**
     * Apply search and filters to a query builder
     *
     * @param Builder $query
     * @param SearchDTO $searchDTO
     * @param array $searchableColumns Columns to search with LIKE operator
     * @return LengthAwarePaginator
     */
    protected function applySearchAndFilters(
        Builder $query,
        SearchDTO $searchDTO,
        array $searchableColumns = []
    ): LengthAwarePaginator {
        // Apply general search (LIKE)
        if ($searchDTO->search && !empty($searchableColumns)) {
            $query->where(function ($q) use ($searchDTO, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'like', "%{$searchDTO->search}%");
                }
            });
        }

        // Apply column filters (exact match with =)
        if (!empty($searchDTO->filters)) {
            foreach ($searchDTO->filters as $column => $value) {
                if (!empty($value)) {
                    $query->where($column, '=', $value);
                }
            }
        }

        // Apply sorting
        $query->orderBy($searchDTO->sort_by, $searchDTO->sort_direction);

        // Return paginated results
        return $query->paginate($searchDTO->per_page);
    }
}
