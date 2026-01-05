<?php

namespace App\Services\Admin;

use App\DTOs\SearchDTO;
use App\Models\ImpersonationLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ImpersonationLogService
{
    /**
     * List impersonation logs with filters.
     */
    public function list(SearchDTO $searchDTO): LengthAwarePaginator
    {
        $query = ImpersonationLog::with('adminUser');

        // Apply filters
        if (!empty($searchDTO->filters)) {
            if (isset($searchDTO->filters['admin_user_id'])) {
                $query->where('admin_user_id', $searchDTO->filters['admin_user_id']);
            }

            if (isset($searchDTO->filters['tenant_id'])) {
                $query->where('tenant_id', $searchDTO->filters['tenant_id']);
            }

            if (isset($searchDTO->filters['status']) && $searchDTO->filters['status'] === 'active') {
                $query->active();
            } elseif (isset($searchDTO->filters['status']) && $searchDTO->filters['status'] === 'completed') {
                $query->completed();
            }
        }

        // Apply search
        if ($searchDTO->search) {
            $query->where(function ($q) use ($searchDTO) {
                $q->where('tenant_name', 'like', "%{$searchDTO->search}%")
                    ->orWhere('target_user_email', 'like', "%{$searchDTO->search}%")
                    ->orWhere('admin_email', 'like', "%{$searchDTO->search}%");
            });
        }

        $query->orderBy($searchDTO->sortBy ?? 'started_at', $searchDTO->sortOrder ?? 'desc');

        return $query->paginate($searchDTO->perPage ?? 20);
    }
}
