<?php

use App\Http\Controllers\VehiclesController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'vehicles'], function () {
    Route::get('/', [VehiclesController::class, 'index'])->name('vehicles.index');
    Route::post('/', [VehiclesController::class, 'store'])->name('vehicles.store');
    Route::get('/filters', [VehiclesController::class, 'findByFilters'])->name('vehicles.filters');
    Route::get('/client/{clientId}', [VehiclesController::class, 'findByClientId'])->name('vehicles.client');
    Route::get('/licence-plate/{licencePlate}', [VehiclesController::class, 'findByLicencePlate'])
        ->name('vehicles.licence-plate');
    Route::get('/vin/{vin}', [VehiclesController::class, 'findByVin'])->name('vehicles.vin');
    Route::get('/{id}', [VehiclesController::class, 'showById'])->name('vehicles.show');
    Route::put('/{id}', [VehiclesController::class, 'update'])->name('vehicles.update');
    Route::delete('/{id}', [VehiclesController::class, 'delete'])->name('vehicles.delete');
});
