<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Each tenant will have isolated data in their own database.
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    
    // Welcome page
    Route::get('/', function () {
        return Inertia::render('Welcome');
    })->name('home');

    // Authentication routes (cada tenant tem seus próprios usuários)
    require __DIR__ . '/auth.php';

    // Protected routes (requer autenticação)
    Route::group(['middleware' => ['auth', 'verified']], function () {
        // Dashboard
        Route::get('dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');

        // Settings
        require __DIR__ . '/settings.php';
        
        // Clients
        require __DIR__ . '/clients.php';
        
        // Vehicles
        require __DIR__ . '/vehicles.php';
        
        // Services
        require __DIR__ . '/services.php';
    });
});
