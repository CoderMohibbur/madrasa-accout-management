<?php

use App\Http\Controllers\Guardian\GuardianInformationalPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GuardianInformationalPortalController::class, 'index'])->name('dashboard');
Route::get('/institution', [GuardianInformationalPortalController::class, 'institution'])->name('institution');
Route::get('/admission', [GuardianInformationalPortalController::class, 'admission'])->name('admission');
