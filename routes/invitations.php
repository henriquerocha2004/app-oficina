<?php

use App\Http\Controllers\InvitationsController;
use Illuminate\Support\Facades\Route;

// Protected routes for managing invitations (auth middleware applied in tenant.php)
Route::get('/invitations', [InvitationsController::class, 'index'])->name('invitations.index');
Route::post('/invitations', [InvitationsController::class, 'store'])->name('invitations.store');
Route::post('/invitations/{invitation}/resend', [InvitationsController::class, 'resend'])->name('invitations.resend');
Route::delete('/invitations/{invitation}', [InvitationsController::class, 'cancel'])->name('invitations.cancel');
