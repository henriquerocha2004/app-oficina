<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'clients'], function () {
    Route::get('/', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/filters', [ClientController::class, 'findByFilters'])->name('clients.filters');
    Route::get('/{id}', [ClientController::class, 'showById'])->name('clients.show');
    Route::put('/{id}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/{id}', [ClientController::class, 'delete'])->name('clients.delete');
});
