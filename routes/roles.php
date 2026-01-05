<?php

use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RolesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Roles
    Route::get('/roles', [RolesController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RolesController::class, 'store'])->name('roles.store');
    Route::put('/roles/{role}', [RolesController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RolesController::class, 'destroy'])->name('roles.destroy');
    Route::post('/roles/{role}/permissions', [RolesController::class, 'syncPermissions'])->name('roles.sync-permissions');
    
    // Permissions
    Route::get('/permissions', [PermissionsController::class, 'index'])->name('permissions.index');
});
