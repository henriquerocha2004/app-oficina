<?php

use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use App\Models\ImpersonationLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Exclude central database from transactions.
 * Central database changes should persist across tests.
 */
beforeEach(function () {
    $this->connectionsToTransact = ['tenant'];
    
    // Create admin user in central database
    $this->admin = \App\Models\AdminUser::factory()->create([
        'name' => 'Super Admin',
        'email' => 'admin@example.com',
    ]);
    
    // Create tenant
    $this->tenant = Tenant::create([
        'id' => 'test-tenant',
        'slug' => 'test-tenant',
        'name' => 'Test Tenant',
    ]);
    
    $this->tenant->domains()->create([
        'domain' => 'test.localhost',
    ]);
    
    // Initialize tenant and create test user
    tenancy()->initialize($this->tenant);
    
    $role = Role::factory()->create(['name' => 'Manager']);
    $this->tenantUser = User::factory()->create([
        'name' => 'Tenant User',
        'email' => 'user@tenant.com',
        'role_id' => $role->id,
    ]);
    
    tenancy()->end();
    
    // Login as admin
    Auth::guard('admin')->login($this->admin);
});

afterEach(function () {
    if (isset($this->tenant)) {
        // End tenancy first
        if (tenancy()->initialized) {
            tenancy()->end();
        }
        
        // Delete tenant (this will drop the database)
        $this->tenant->delete();
    }
});

test('admin can impersonate user', function () {
    $response = $this->post(route('admin.impersonate', [
        'tenant' => $this->tenant->id,
        'user' => $this->tenantUser->id,
    ]));
    
    $response->assertRedirect();
    
    // Check session has impersonation data
    expect(session()->has('original_admin_id'))->toBeTrue();
    expect(session('impersonating_tenant_id'))->toBe($this->tenant->id);
});

test('impersonation creates log entry', function () {
    $this->post(route('admin.impersonate', [
        'tenant' => $this->tenant->id,
        'user' => $this->tenantUser->id,
    ]));
    
    $log = ImpersonationLog::where('admin_user_id', $this->admin->id)
        ->where('tenant_id', $this->tenant->id)
        ->first();
    
    expect($log)->not->toBeNull()
        ->and($log->started_at)->not->toBeNull()
        ->and($log->ended_at)->toBeNull();
});

test('can stop impersonation', function () {
    // Start impersonation
    session([
        'original_admin_id' => $this->admin->id,
        'impersonating_tenant_id' => $this->tenant->id,
        'impersonating_user_id' => $this->tenantUser->id,
        'impersonation_log_id' => 'test-log-id',
    ]);
    
    $response = $this->post(route('admin.stop-impersonating'));
    
    $response->assertRedirect();
    
    // Check session was cleared
    expect(session()->has('original_admin_id'))->toBeFalse();
});

// TODO: Uncomment when Admin/ImpersonationLogs.vue is created and built
// test('can view impersonation logs', function () {
//     ImpersonationLog::factory()->count(3)->create([
//         'admin_user_id' => $this->admin->id,
//     ]);
//     
//     $response = $this->get(route('admin.impersonation-logs.index'));
//     
//     $response->assertOk();
//     $response->assertInertia(fn ($page) => 
//         $page->component('admin/impersonation-logs/Index')
//             ->has('logs')
//     );
// });

