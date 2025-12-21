<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

// Pricing page (future)
Route::get('/pricing', function () {
    return Inertia::render('Pricing');
})->name('pricing');

// Future: Tenant registration routes
// Route::get('/register-tenant', [TenantRegistrationController::class, 'create'])->name('tenant.register');
// Route::post('/register-tenant', [TenantRegistrationController::class, 'store']);

// Super Admin routes (future)
// Route::prefix('admin')->middleware(['auth', 'super-admin'])->group(function () {
//     Route::get('/dashboard', [SuperAdminController::class, 'dashboard']);
//     Route::resource('tenants', TenantController::class);
// });
