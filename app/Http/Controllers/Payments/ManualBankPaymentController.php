<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\StoreManualBankPaymentRequest;
use App\Models\Payment;
use App\Models\StudentFeeInvoice;
use App\Services\Payments\PaymentWorkflowService;
use Illuminate\Contracts\View\View;

class ManualBankPaymentController extends Controller
{
    public function __construct(
        private readonly PaymentWorkflowService $paymentWorkflowService,
    ) {
    }

    public function store(StoreManualBankPaymentRequest $request)
    {
        $invoice = StudentFeeInvoice::query()->findOrFail($request->integer('invoice_id'));
        $payment = $this->paymentWorkflowService->submitManualBankRequest($request->user(), $invoice, $request->validated());

        return redirect()
            ->route('payments.manual-bank.show', $payment)
            ->with('success', 'Manual bank evidence submitted. A management review is now required.');
    }

    public function show(Payment $payment): View
    {
        $payment->loadMissing(['payable.student', 'receipt', 'reviewer']);

        return view('payments.manual-bank.show', [
            'payment' => $payment,
            'invoice' => $payment->payable,
        ]);
    }
}
