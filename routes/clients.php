<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'clients', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/', [ClientController::class, 'index'])->name('clients.index');
});
