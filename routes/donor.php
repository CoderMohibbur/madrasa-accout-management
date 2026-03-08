<?php

use App\Http\Controllers\Donor\DonorPortalController;
use Illuminate\Support\Facades\Route;

Route::controller(DonorPortalController::class)->group(function (): void {
    Route::get('/', 'index')->name('dashboard');
    Route::get('/donations', 'donations')->name('donations.index');
    Route::get('/receipts', 'receipts')->name('receipts.index');
});
