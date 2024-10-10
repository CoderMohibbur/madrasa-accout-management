<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\ExpensController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\LenderController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AddClassController;
use App\Http\Controllers\AddMonthController;
use App\Http\Controllers\AddAcademyController;
use App\Http\Controllers\AddSectionController;
use App\Http\Controllers\AddCatagoryController;
use App\Http\Controllers\AddFessTypeController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\AddRegistrationFessController;

    Route::get('/', function () {
    return view('welcome');
    });
     // dashboard Route
    Route::get('/dashboard', function () {
    return view('dashboard');
     })->middleware(['auth', 'verified'])->name('dashboard');
    // Profile Route
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
     //AddRegistrationFess
     Route::get('/add-registration',[AddRegistrationFessController::class, 'index'])->name('add_registration.index');
     Route::post('/add-registration', [AddRegistrationFessController::class, 'store'])->name('add_registration.store');
     Route::get('/add-registration/{id}/edit', [AddRegistrationFessController::class, 'edit'])->name('add_registration.edit');
     Route::put('/add-registration/{id}', [AddRegistrationFessController::class, 'update'])->name('add_registration.update');
     Route::delete('/add-registration/{id}', [AddRegistrationFessController::class, 'destroy'])->name('add_registration.destroy');
    // Students
    Route::resource('students', StudentController::class);
    // Doner
    Route::resource(name: 'donors', controller: DonorController::class);
    //Account
    Route::resource(name: 'account', controller: AccountController::class);
    // Expens
    Route::resource(name: 'expens', controller: ExpensController::class);
     // Add catagory
     Route::get('/add-catagory', [AddCatagoryController::class, 'index'])->name('add_catagory.index');
     Route::post('/add-catagory', [AddCatagoryController::class, 'store'])->name('add_catagory.store');
     Route::get('/add-catagory/{id}/edit', [AddCatagoryController::class, 'edit'])->name('add_catagory.edit');
     Route::put('/add-catagory/{id}', [AddCatagoryController::class, 'update'])->name('add_catagory.update');
     Route::delete('/add-catagory/{id}', [AddCatagoryController::class, 'destroy'])->name('add_catagory.destroy');
     //Income
     Route::resource(name: 'income', controller: IncomeController::class);
    //Lender
      Route::resource(name: 'lender', controller: LenderController::class);

    // TransactionsController
     Route::get('/add-student-fees', [TransactionsController::class, 'index'])->name('add_student_fees.index');
     Route::post('/add-student-fees', [TransactionsController::class, 'store'])->name('add_student_fees.store');
     Route::get('/add-student-fees/{id}/edit', [TransactionsController::class, 'edit'])->name('add_student_fees.edit');
     Route::put('/add-student-fees/{id}', [TransactionsController::class, 'update'])->name('add_student_fees.update');
     Route::delete('/add-student-fees/{id}', [TransactionsController::class, 'destroy'])->name('add_student_fees.destroy');


});


require __DIR__ . '/auth.php';









