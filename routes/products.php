<?php

use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'products'], function () {
    Route::get('/', [ProductsController::class, 'index'])->name('products.index');
    Route::post('/', [ProductsController::class, 'store'])->name('products.store');
    Route::get('/filters', [ProductsController::class, 'findByFilters'])->name('products.filters');
    Route::get('/low-stock', [ProductsController::class, 'getLowStock'])->name('products.lowStock');
    Route::get('/active', [ProductsController::class, 'getActiveProducts'])->name('products.active');
    Route::get('/{id}', [ProductsController::class, 'showById'])->name('products.show');
    Route::put('/{id}', [ProductsController::class, 'update'])->name('products.update');
    Route::delete('/{id}', [ProductsController::class, 'delete'])->name('products.delete');
    Route::post('/{id}/adjust-stock', [ProductsController::class, 'adjustStock'])->name('products.adjustStock');

    // Product suppliers management
    Route::get('/{id}/suppliers', [ProductsController::class, 'getSuppliers'])->name('products.suppliers.index');
    Route::post('/{id}/suppliers', [ProductsController::class, 'attachSupplier'])->name('products.suppliers.attach');
    Route::put('/{productId}/suppliers/{supplierId}', [ProductsController::class, 'updateSupplier'])->name('products.suppliers.update');
    Route::delete('/{productId}/suppliers/{supplierId}', [ProductsController::class, 'detachSupplier'])->name('products.suppliers.detach');
});
