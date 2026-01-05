<?php

namespace App\Services;

use App\Models\AdminUser;
use App\Models\ImpersonationLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonationService
{
    /**
     * Start impersonating a user in a tenant.
     */
    public function startImpersonation(AdminUser $admin, Tenant $tenant, User $user): ImpersonationLog
    {
        // Create impersonation log
        $log = ImpersonationLog::create([
            'admin_user_id' => $admin->id,
            'admin_email' => $admin->email,
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'target_user_id' => $user->id,
            'target_user_name' => $user->name,
            'target_user_email' => $user->email,
            'started_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Set session variables
        Session::put('original_admin_id', $admin->id);
        Session::put('impersonating_tenant_id', $tenant->id);
        Session::put('impersonating_user_id', $user->id);
        Session::put('impersonation_log_id', $log->id);

        return $log;
    }

    /**
     * Stop the current impersonation session.
     */
    public function stopImpersonation(): void
    {
        $logId = Session::get('impersonation_log_id');

        if ($logId) {
            $log = ImpersonationLog::find($logId);
            if ($log && $log->isActive()) {
                $log->update(['ended_at' => now()]);
            }
        }

        // Clear session
        Session::forget('original_admin_id');
        Session::forget('impersonating_tenant_id');
        Session::forget('impersonating_user_id');
        Session::forget('impersonation_log_id');
    }

    /**
     * Get paginated impersonation logs for an admin user.
     */
    public function getPaginatedLogs(AdminUser $admin, int $perPage = 15)
    {
        return ImpersonationLog::where('admin_user_id', $admin->id)
            ->latest('started_at')
            ->paginate($perPage);
    }

    /**
     * Get all impersonation logs (for super admins).
     */
    public function getAllPaginatedLogs(int $perPage = 15)
    {
        return ImpersonationLog::with('adminUser')
            ->latest('started_at')
            ->paginate($perPage);
    }
}
