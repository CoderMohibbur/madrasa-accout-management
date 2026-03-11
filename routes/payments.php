<?php

use App\Http\Controllers\Management\ManualBankPaymentReviewController;
use App\Http\Controllers\Payments\ManualBankPaymentController;
use App\Http\Controllers\Payments\ShurjopayPaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'guardian.protected'])->group(function (): void {
    Route::post('/payments/shurjopay/initiate', [ShurjopayPaymentController::class, 'initiate'])
        ->name('payments.shurjopay.initiate');

    Route::post('/payments/manual-bank/requests', [ManualBankPaymentController::class, 'store'])
        ->name('payments.manual-bank.requests.store');
});

Route::middleware(['auth'])->group(function (): void {
    Route::get('/payments/shurjopay/return/success', [ShurjopayPaymentController::class, 'success'])
        ->name('payments.shurjopay.return.success');
    Route::get('/payments/shurjopay/return/fail', [ShurjopayPaymentController::class, 'fail'])
        ->name('payments.shurjopay.return.fail');
    Route::get('/payments/shurjopay/return/cancel', [ShurjopayPaymentController::class, 'cancel'])
        ->name('payments.shurjopay.return.cancel');
    Route::get('/payments/manual-bank/{payment}', [ManualBankPaymentController::class, 'show'])
        ->middleware('can:view,payment')
        ->name('payments.manual-bank.show');
});

Route::post('/payments/shurjopay/ipn', [ShurjopayPaymentController::class, 'ipn'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
    ->name('payments.shurjopay.ipn');

Route::middleware(['auth', 'verified', 'role:management'])
    ->prefix('management/payments/manual-bank')
    ->as('management.payments.manual-bank.')
    ->group(function (): void {
        Route::get('/', [ManualBankPaymentReviewController::class, 'index'])->name('index');
        Route::post('/{payment}/approve', [ManualBankPaymentReviewController::class, 'approve'])->name('approve');
        Route::post('/{payment}/reject', [ManualBankPaymentReviewController::class, 'reject'])->name('reject');
    });
