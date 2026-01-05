<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ImpersonationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ImpersonationController extends Controller
{
    public function __construct(
        private ImpersonationService $impersonationService
    ) {
    }

    /**
     * Start impersonating a user.
     */
    public function impersonate(Request $request, string $tenantId, string $userId): RedirectResponse
    {
        try {
            // Get current admin user
            $admin = Auth::guard('admin')->user();
            if (!$admin) {
                throw new \Exception('Admin user not authenticated');
            }

            $tenant = Tenant::findOrFail($tenantId);

            // Initialize tenant to access its users
            tenancy()->initialize($tenant);

            $targetUser = User::findOrFail($userId);

            // End tenant context temporarily
            tenancy()->end();

            // Start impersonation
            $this->impersonationService->startImpersonation($admin, $tenant, $targetUser);

            return redirect()->route('dashboard')
                ->with('success', "Você está impersonando {$targetUser->name}");
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['error' => 'Erro ao impersonar usuário: ' . $e->getMessage()]);
        }
    }

    /**
     * Stop impersonation.
     */
    public function stopImpersonation(): RedirectResponse
    {
        try {
            $this->impersonationService->stopImpersonation();

            return redirect()->route('admin.dashboard')
                ->with('success', 'Impersonation finalizado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erro ao finalizar impersonation: ' . $e->getMessage()]);
        }
    }

    /**
     * Display impersonation logs.
     */
    public function logs(Request $request): InertiaResponse
    {
        $admin = Auth::guard('admin')->user();
        
        $logs = $this->impersonationService->getPaginatedLogs($admin);

        return Inertia::render('Admin/ImpersonationLogs', [
            'logs' => $logs,
        ]);
    }
}
