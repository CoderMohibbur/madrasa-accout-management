<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\AddClassController;
use App\Http\Controllers\AddMonthController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AddAcademyController;
use App\Http\Controllers\AddSectionController;
use App\Http\Controllers\AddFessTypeController;
use App\Http\Controllers\StudentController;

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

    //add-academy conroller

    Route::get('/add-academy',[AddAcademyController::class, 'index'])->name('add_academy.index');
    Route::post('/add-academy', [AddAcademyController::class, 'store'])->name('add_academy.store');
    Route::get('/add-academy/{id}/edit', [AddAcademyController::class, 'edit'])->name('add_academy.edit');
    Route::put('/add-academy/{id}', [AddAcademyController::class, 'update'])->name('add_academy.update');
    Route::delete('/add-academy/{id}', [AddAcademyController::class, 'destroy'])->name('add_academy.destroy');

    // Accounting


    Route::get('/accounting',[AccountingController::class, 'index'])->name('accounting.index');

    //add-academy conroller

    Route::get('/add-fees-type',[AddFessTypeController::class, 'index'])->name('add_fees_type.index');
    Route::post('/add-fees-type', [AddFessTypeController::class, 'store'])->name('add_fees_type.store');
    Route::get('/add-fees-type/{id}/edit', [AddFessTypeController::class, 'edit'])->name('add_fees_type.edit');
    Route::put('/add-fees-type/{id}', [AddFessTypeController::class, 'update'])->name('add_fees_type.update');
    Route::delete('/add-fees-type/{id}', [AddFessTypeController::class, 'destroy'])->name('add_fees_type.destroy');

    // Section
    Route::get('/section',[AddSectionController::class, 'index'])->name('Section.index');
    Route::post('/section', [AddSectionController::class, 'store'])->name('Section.store');
    Route::get('/section/{id}/edit', [AddSectionController::class, 'edit'])->name('Section.edit');
    Route::put('/section/{id}', [AddSectionController::class, 'update'])->name('Section.update');
    Route::delete('/section/{id}', [AddSectionController::class, 'destroy'])->name('Section.destroy');



    // Students
    // Route::get('/add-student',action: [StudentController::class, 'index'])->name('add_student.index');
    // Route::post('/add-student',action: [StudentController::class, 'store'])->name('add_student.index');
    // Route::get('/list-student',action: [StudentController::class, 'index'])->name('add_liststudent.index');

    Route::resource('students', StudentController::class);

});


require __DIR__ . '/auth.php';
