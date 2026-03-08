<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\ReviewManualBankPaymentRequest;
use App\Models\Payment;
use App\Services\Payments\PaymentWorkflowService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ManualBankPaymentReviewController extends Controller
{
    public function __construct(
        private readonly PaymentWorkflowService $paymentWorkflowService,
    ) {
    }

    public function index(Request $request): View
    {
        $payments = Payment::query()
            ->with(['user', 'payable.student', 'reviewer', 'receipt'])
            ->where('provider', 'manual_bank')
            ->whereIn('status', [
                Payment::STATUS_AWAITING_MANUAL_PAYMENT,
                Payment::STATUS_MANUAL_REVIEW,
                Payment::STATUS_FAILED,
            ])
            ->orderByRaw("case when status = 'awaiting_manual_payment' then 0 when status = 'manual_review' then 1 else 2 end")
            ->orderByDesc('initiated_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('management.manual-bank-payments.index', compact('payments'));
    }

    public function approve(ReviewManualBankPaymentRequest $request, Payment $payment)
    {
        $this->paymentWorkflowService->approveManualBank($request->user(), $payment, $request->validated());

        return redirect()
            ->route('management.payments.manual-bank.index')
            ->with('success', 'Manual bank payment approved and finalized.');
    }

    public function reject(ReviewManualBankPaymentRequest $request, Payment $payment)
    {
        $this->paymentWorkflowService->rejectManualBank($request->user(), $payment, $request->validated());

        return redirect()
            ->route('management.payments.manual-bank.index')
            ->with('success', 'Manual bank payment rejected.');
    }
}
