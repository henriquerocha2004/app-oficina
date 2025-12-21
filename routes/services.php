<?php

use App\Http\Controllers\ServicesController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'services'], function () {
    Route::get('/', [ServicesController::class, 'index'])->name('services.index');
    Route::post('/', [ServicesController::class, 'store'])->name('services.store');
    Route::get('/filters', [ServicesController::class, 'findByFilters'])->name('services.filters');
    Route::get('/active', [ServicesController::class, 'listActive'])->name('services.active');
    Route::get('/category/{category}', [ServicesController::class, 'findByCategory'])
        ->name('services.category');
    Route::get('/{id}', [ServicesController::class, 'showById'])->name('services.show');
    Route::put('/{id}', [ServicesController::class, 'update'])->name('services.update');
    Route::patch('/{id}/toggle-active', [ServicesController::class, 'toggleActive'])
        ->name('services.toggle-active');
    Route::delete('/{id}', [ServicesController::class, 'delete'])->name('services.delete');
});
