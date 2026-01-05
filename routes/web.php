<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminSubscriptionPlansController;
use App\Http\Controllers\Admin\AdminTenantsController;
use App\Http\Controllers\Admin\ImpersonationController;

/*
|--------------------------------------------------------------------------
| Central App Routes
|--------------------------------------------------------------------------
|
| These routes are for the CENTRAL domain (app-oficina.local)
| They handle tenant registration, pricing pages, marketing, etc.
|
| Tenant-specific routes are in routes/tenant.php
|
*/

// Landing page / Marketing
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => true, // Permitir registro de novos tenants
    ]);
})->name('home');

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/

// Admin Authentication
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected Admin Routes
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Subscription Plans Management
        Route::prefix('plans')->name('plans.')->group(function () {
            Route::get('/', [AdminSubscriptionPlansController::class, 'index'])->name('index');
            Route::get('/filters', [AdminSubscriptionPlansController::class, 'findByFilters'])->name('filters');
            Route::post('/', [AdminSubscriptionPlansController::class, 'store'])->name('store');
            Route::put('/{id}', [AdminSubscriptionPlansController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminSubscriptionPlansController::class, 'destroy'])->name('destroy');
        });

        // Tenants Management
        Route::prefix('tenants')->name('tenants.')->group(function () {
            Route::get('/', [AdminTenantsController::class, 'index'])->name('index');
            Route::get('/filters', [AdminTenantsController::class, 'findByFilters'])->name('filters');
            Route::post('/', [AdminTenantsController::class, 'store'])->name('store');
            Route::put('/{id}', [AdminTenantsController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminTenantsController::class, 'destroy'])->name('destroy');
        });

        // Impersonation
        Route::post('/tenants/{tenant}/users/{user}/impersonate', [ImpersonationController::class, 'impersonate'])->name('impersonate');
        Route::post('/stop-impersonating', [ImpersonationController::class, 'stopImpersonation'])->name('stop-impersonating');

        // Impersonation Logs
        Route::get('/impersonation-logs', [ImpersonationController::class, 'logs'])->name('impersonation-logs.index');
    });
});

// Future: Tenant registration routes
// Route::get('/register-tenant', [TenantRegistrationController::class, 'create'])->name('tenant.register');
// Route::post('/register-tenant', [TenantRegistrationController::class, 'store']);
//     Route::get('/dashboard', [SuperAdminController::class, 'dashboard']);
//     Route::resource('tenants', TenantController::class);
// });
