<?php

namespace App\DTOs\Admin;

readonly class DashboardStatsDTO
{
    public function __construct(
        public int $totalTenants,
        public int $activeTenants,
        public int $totalPlans,
    ) {
    }

    public function toArray(): array
    {
        return [
            'total_tenants' => $this->totalTenants,
            'active_tenants' => $this->activeTenants,
            'total_plans' => $this->totalPlans,
        ];
    }
}
