<?php

namespace App\Services\Admin;

use App\DTOs\Admin\DashboardStatsDTO;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;

class AdminDashboardService
{
    public function getStats(): DashboardStatsDTO
    {
        return new DashboardStatsDTO(
            totalTenants: Tenant::count(),
            activeTenants: Tenant::where('is_active', true)->count(),
            totalPlans: SubscriptionPlan::count(),
        );
    }
}
