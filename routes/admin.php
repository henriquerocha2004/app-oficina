<?php

use App\Http\Controllers\Admin\AdminPlansController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/plans', [AdminPlansController::class, 'index'])->name('plans.index');
    Route::post('/plans', [AdminPlansController::class, 'store'])->name('plans.store');
    Route::put('/plans/{id}', [AdminPlansController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{id}', [AdminPlansController::class, 'destroy'])->name('plans.destroy');
    Route::get('/plans/filters', [AdminPlansController::class, 'list'])->name('plans.list');
});
