<?php

namespace App\Services\Admin;

use App\Models\ImpersonationLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImpersonationService
{
    /**
     * Start impersonating a user in a tenant.
     */
    public function impersonate(Tenant $tenant, User $targetUser): array
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            throw new \RuntimeException('Admin não autenticado.');
        }

        // Store impersonation data in session
        session([
            'impersonating' => true,
            'original_admin_id' => $admin->id,
            'original_admin_email' => $admin->email,
            'impersonating_tenant_id' => $tenant->id,
            'impersonating_tenant_name' => $tenant->name,
            'impersonating_user_id' => $targetUser->id,
            'impersonating_user_name' => $targetUser->name,
            'impersonating_user_email' => $targetUser->email,
        ]);

        // Create impersonation log
        $log = ImpersonationLog::create([
            'admin_user_id' => $admin->id,
            'admin_email' => $admin->email,
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'target_user_id' => $targetUser->id,
            'target_user_name' => $targetUser->name,
            'target_user_email' => $targetUser->email,
            'started_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        session(['impersonation_log_id' => $log->id]);

        // Logout admin
        Auth::guard('admin')->logout();

        // Initialize tenant context
        tenancy()->initialize($tenant);

        // Login as target user
        Auth::guard('web')->loginUsingId($targetUser->id);

        return [
            'tenant_id' => $tenant->id,
            'user_id' => $targetUser->id,
            'log_id' => $log->id,
        ];
    }

    /**
     * Stop impersonation and return to admin.
     */
    public function stopImpersonation(): void
    {
        if (!$this->isImpersonating()) {
            throw new \RuntimeException('Não está em modo de impersonation.');
        }

        $originalAdminId = session('original_admin_id');
        $logId = session('impersonation_log_id');

        // Update impersonation log
        if ($logId) {
            $log = ImpersonationLog::find($logId);
            if ($log) {
                $log->update(['ended_at' => now()]);
            }
        }

        // End tenant context
        if (tenancy()->initialized) {
            tenancy()->end();
        }

        // Logout tenant user
        Auth::guard('web')->logout();

        // Re-authenticate admin
        if ($originalAdminId) {
            Auth::guard('admin')->loginUsingId($originalAdminId);
        }

        // Clear impersonation session data
        session()->forget([
            'impersonating',
            'original_admin_id',
            'original_admin_email',
            'impersonating_tenant_id',
            'impersonating_tenant_name',
            'impersonating_user_id',
            'impersonating_user_name',
            'impersonating_user_email',
            'impersonation_log_id',
        ]);
    }

    /**
     * Check if currently impersonating.
     */
    public function isImpersonating(): bool
    {
        return session('impersonating', false) === true;
    }

    /**
     * Get impersonation data.
     */
    public function getImpersonationData(): ?array
    {
        if (!$this->isImpersonating()) {
            return null;
        }

        return [
            'admin_id' => session('original_admin_id'),
            'admin_email' => session('original_admin_email'),
            'tenant_id' => session('impersonating_tenant_id'),
            'tenant_name' => session('impersonating_tenant_name'),
            'user_id' => session('impersonating_user_id'),
            'user_name' => session('impersonating_user_name'),
            'user_email' => session('impersonating_user_email'),
        ];
    }
}
