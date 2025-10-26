<?php

use App\Http\Controllers\CarsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'cars'], function () {
    Route::get('/', [CarsController::class, 'index'])->name('cars.index');
    Route::post('/', [CarsController::class, 'store'])->name('cars.store');
    Route::get('/filters', [CarsController::class, 'findByFilters'])->name('cars.filters');
    Route::get('/client/{clientId}', [CarsController::class, 'findByClientId'])->name('cars.client');
    Route::get('/licence-plate/{licencePlate}', [CarsController::class, 'findByLicencePlate'])
        ->name('cars.licence-plate');
    Route::get('/vin/{vin}', [CarsController::class, 'findByVin'])->name('cars.vin');
    Route::get('/{id}', [CarsController::class, 'showById'])->name('cars.show');
    Route::put('/{id}', [CarsController::class, 'update'])->name('cars.update');
    Route::delete('/{id}', [CarsController::class, 'delete'])->name('cars.delete');
});
