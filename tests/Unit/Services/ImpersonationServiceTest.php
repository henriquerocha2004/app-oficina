<?php

use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use App\Services\Admin\ImpersonationService;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->impersonationService = app(ImpersonationService::class);
    
    // Create a test tenant
    $this->tenant = Tenant::create([
        'id' => 'test-tenant',
        'name' => 'Test Tenant',
    ]);
    
    // Create admin user (in central database)
    $this->admin = \App\Models\AdminUser::factory()->create([
        'name' => 'Super Admin',
        'email' => 'admin@example.com',
    ]);
    
    // Initialize tenant and create user
    tenancy()->initialize($this->tenant);
    
    $role = Role::factory()->create([
        'name' => 'Manager',
        'slug' => 'manager',
    ]);
    
    $this->tenantUser = User::factory()->create([
        'name' => 'Tenant User',
        'email' => 'user@tenant.com',
        'role_id' => $role->id,
    ]);
    
    tenancy()->end();
});

it('can start impersonation session', function () {
    Auth::guard('admin')->login($this->admin);
    
    $this->impersonationService->impersonate($this->tenant, $this->tenantUser);
    
    // Check session data
    expect(session()->has('original_admin_id'))->toBeTrue();
    expect(session('original_admin_id'))->toBe($this->admin->id);
    expect(session('impersonating_tenant_id'))->toBe($this->tenant->id);
    expect(session('impersonating_user_id'))->toBe($this->tenantUser->id);
    
    // Check user is authenticated in web guard
    expect(Auth::guard('web')->check())->toBeTrue();
    expect(Auth::guard('web')->id())->toBe($this->tenantUser->id);
});

it('can check if currently impersonating', function () {
    expect($this->impersonationService->isImpersonating())->toBeFalse();
    
    session([
        'original_admin_id' => $this->admin->id,
        'impersonating_tenant_id' => $this->tenant->id,
        'impersonating_user_id' => $this->tenantUser->id,
    ]);
    
    expect($this->impersonationService->isImpersonating())->toBeTrue();
});

it('can get impersonation data', function () {
    session([
        'original_admin_id' => $this->admin->id,
        'impersonating_tenant_id' => $this->tenant->id,
        'impersonating_user_id' => $this->tenantUser->id,
        'impersonation_log_id' => 'test-log-id',
    ]);
    
    $data = $this->impersonationService->getImpersonationData();
    
    expect($data)->toBeArray()
        ->toHaveKey('admin_id')
        ->toHaveKey('tenant_id')
        ->toHaveKey('user_id');
    
    expect($data['admin_id'])->toBe($this->admin->id);
    expect($data['tenant_id'])->toBe($this->tenant->id);
});

it('can stop impersonation and restore admin session', function () {
    // Setup impersonation state
    session([
        'original_admin_id' => $this->admin->id,
        'impersonating_tenant_id' => $this->tenant->id,
        'impersonating_user_id' => $this->tenantUser->id,
        'impersonation_log_id' => 'test-log-id',
    ]);
    
    // Initialize tenant and login user
    tenancy()->initialize($this->tenant);
    Auth::guard('web')->login($this->tenantUser);
    
    $this->impersonationService->stopImpersonation();
    
    // Check session was cleared
    expect(session()->has('original_admin_id'))->toBeFalse();
    expect(session()->has('impersonating_tenant_id'))->toBeFalse();
    expect(session()->has('impersonating_user_id'))->toBeFalse();
    
    // Check admin is logged back in
    expect(Auth::guard('admin')->check())->toBeTrue();
    expect(Auth::guard('admin')->id())->toBe($this->admin->id);
});

it('creates impersonation log when starting impersonation', function () {
    Auth::guard('admin')->login($this->admin);
    
    $this->impersonationService->impersonate($this->tenant, $this->tenantUser);
    
    // Check log was created in central database
    $log = \App\Models\ImpersonationLog::where('admin_id', $this->admin->id)
        ->where('tenant_id', $this->tenant->id)
        ->where('user_id', $this->tenantUser->id)
        ->first();
    
    expect($log)->not->toBeNull();
    expect($log->started_at)->not->toBeNull();
    expect($log->ended_at)->toBeNull(); // Still active
});

it('updates impersonation log when stopping impersonation', function () {
    Auth::guard('admin')->login($this->admin);
    
    $this->impersonationService->impersonate($this->tenant, $this->tenantUser);
    
    $logId = session('impersonation_log_id');
    
    $this->impersonationService->stopImpersonation();
    
    $log = \App\Models\ImpersonationLog::find($logId);
    
    expect($log->ended_at)->not->toBeNull();
});
