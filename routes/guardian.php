<?php

use App\Http\Controllers\Guardian\GuardianPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GuardianPortalController::class, 'index'])->name('dashboard');
Route::get('/students/{student}', [GuardianPortalController::class, 'student'])->name('students.show');
Route::get('/invoices', [GuardianPortalController::class, 'invoices'])->name('invoices.index');
Route::get('/invoices/{invoice}', [GuardianPortalController::class, 'invoice'])->name('invoices.show');
Route::get('/history', [GuardianPortalController::class, 'history'])->name('history.index');
