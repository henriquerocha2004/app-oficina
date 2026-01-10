<?php

use App\Http\Controllers\SuppliersController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'suppliers'], function () {
    Route::get('/', [SuppliersController::class, 'index'])->name('suppliers.index');
    Route::post('/', [SuppliersController::class, 'store'])->name('suppliers.store');
    Route::get('/filters', [SuppliersController::class, 'findByFilters'])->name('suppliers.filters');
    Route::get('/{id}', [SuppliersController::class, 'showById'])->name('suppliers.show');
    Route::put('/{id}', [SuppliersController::class, 'update'])->name('suppliers.update');
    Route::delete('/{id}', [SuppliersController::class, 'delete'])->name('suppliers.delete');
});
