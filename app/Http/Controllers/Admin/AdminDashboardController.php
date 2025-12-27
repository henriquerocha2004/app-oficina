<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminDashboardService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __construct(
        private AdminDashboardService $dashboardService
    ) {
    }

    public function index(): Response
    {
        try {
            $stats = $this->dashboardService->getStats();

            return Inertia::render('admin/Dashboard', [
                'stats' => $stats->toArray(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao carregar dashboard admin', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Inertia::render('admin/Dashboard', [
                'stats' => [
                    'total_tenants' => 0,
                    'active_tenants' => 0,
                    'total_plans' => 0,
                ],
                'error' => 'Erro ao carregar estatísticas.',
            ]);
        }
    }
}
