<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddClassController;
use App\Http\Controllers\AddMonthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name(name: 'profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Add Class Route
    Route::get('/add-class', [AddClassController::class, 'index'])->name('add_class.index');
    Route::post('/add-class', [AddClassController::class, 'store'])->name('add_class.store');
    Route::get('/add-class/{id}/edit', [AddClassController::class, 'edit'])->name('add_class.edit');
    Route::put('/add-class/{id}', [AddClassController::class, 'update'])->name('add_class.update');
    Route::delete('/add-class/{id}', [AddClassController::class, 'destroy'])->name('add_class.destroy');

    // Add Month Route
    Route::get('/add-month', [AddMonthController::class, 'index'])->name('add_month.index');
    Route::post('/add-month', [AddMonthController::class, 'store'])->name('add_month.store');
    Route::get('/add-month/{id}/edit', [AddMonthController::class, 'edit'])->name('add_month.edit');
    Route::put('/add-month/{id}', [AddMonthController::class, 'update'])->name('add_month.update');
    Route::delete('/add-month/{id}', [AddMonthController::class, 'destroy'])->name('add_month.destroy');
});


require __DIR__ . '/auth.php';
