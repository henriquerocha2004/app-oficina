<?php

use App\Http\Controllers\StockMovementsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'stock-movements'], function () {
    Route::get('/', [StockMovementsController::class, 'index'])->name('stockMovements.index');
    Route::get('/filters', [StockMovementsController::class, 'findByFilters'])->name('stockMovements.filters');
    Route::get('/product/{productId}', [StockMovementsController::class, 'getByProduct'])->name('stockMovements.byProduct');
});
