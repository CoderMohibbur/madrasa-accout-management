<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\InitiateShurjopayPaymentRequest;
use App\Models\StudentFeeInvoice;
use App\Services\Payments\PaymentFlowResult;
use App\Services\Payments\PaymentWorkflowService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShurjopayPaymentController extends Controller
{
    public function __construct(
        private readonly PaymentWorkflowService $paymentWorkflowService,
    ) {
    }

    public function initiate(InitiateShurjopayPaymentRequest $request)
    {
        $invoice = StudentFeeInvoice::query()->findOrFail($request->integer('invoice_id'));
        $result = $this->paymentWorkflowService->initiateShurjopay($request->user(), $invoice);

        return redirect()->away($result['checkout_url']);
    }

    public function success(Request $request): View
    {
        $result = $this->paymentWorkflowService->handleShurjopaySuccessReturn($request->user(), $request->all());

        return view('payments.shurjopay.status', $this->statusViewData($result, 'success'));
    }

    public function fail(Request $request): View
    {
        $result = $this->paymentWorkflowService->handleShurjopayFailureReturn($request->user(), $request->all());

        return view('payments.shurjopay.status', $this->statusViewData($result, 'fail'));
    }

    public function cancel(Request $request): View
    {
        $result = $this->paymentWorkflowService->handleShurjopayCancellation($request->user(), $request->all());

        return view('payments.shurjopay.status', $this->statusViewData($result, 'cancel'));
    }

    public function ipn(Request $request): JsonResponse
    {
        $result = $this->paymentWorkflowService->handleShurjopayIpn($request->all());

        return response()->json([
            'status' => $result->status,
            'message' => $result->message,
            'payment_id' => $result->payment?->getKey(),
        ], $result->status === 'failed' ? 422 : 200);
    }

    private function statusViewData(PaymentFlowResult $result, string $routeOutcome): array
    {
        $descriptions = [
            'success' => 'Sandbox-only shurjoPay return handling. Browser redirects never finalize a payment without server-side verification.',
            'fail' => 'This page reflects a non-successful or non-verified sandbox shurjoPay outcome.',
            'cancel' => 'Browser cancellation is non-authoritative. Laravel keeps waiting for any later provider confirmation before treating the payment as settled.',
        ];

        return [
            'title' => match ($result->status) {
                'paid' => 'Payment Verified',
                'failed' => 'Payment Not Verified',
                'cancelled' => 'Payment Cancelled',
                'manual_review' => 'Manual Review Needed',
                default => 'Payment Pending',
            },
            'description' => $descriptions[$routeOutcome],
            'result' => $result,
            'payment' => $result->payment,
            'invoice' => $result->payment?->payable,
        ];
    }
}
