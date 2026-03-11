<?php

namespace App\Http\Controllers\Donations;

use App\Http\Controllers\Controller;
use App\Models\DonationIntent;
use App\Services\Donations\DonationCheckoutService;
use App\Services\Payments\PaymentFlowResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DonationCheckoutController extends Controller
{
    public function __construct(
        private readonly DonationCheckoutService $donationCheckoutService,
    ) {
    }

    public function checkout(Request $request): RedirectResponse
    {
        $draft = $request->session()->get(GuestDonationEntryController::DRAFT_SESSION_KEY);

        if (! is_array($draft) || empty($draft['amount'])) {
            throw ValidationException::withMessages([
                'payment' => ['Start the donation from the entry form before continuing to checkout.'],
            ]);
        }

        $result = $this->donationCheckoutService->beginCheckout($request, $draft);

        return redirect()->away($result['checkout_url']);
    }

    public function status(Request $request, string $publicReference): View
    {
        return view('donations.status', $this->donationCheckoutService->statusViewData($request, $publicReference));
    }

    public function success(Request $request): RedirectResponse
    {
        return $this->redirectToStatus($request, $this->donationCheckoutService->handleShurjopaySuccessReturn($request->user(), $request->all()));
    }

    public function fail(Request $request): RedirectResponse
    {
        return $this->redirectToStatus($request, $this->donationCheckoutService->handleShurjopayFailureReturn($request->user(), $request->all()));
    }

    public function cancel(Request $request): RedirectResponse
    {
        return $this->redirectToStatus($request, $this->donationCheckoutService->handleShurjopayCancellation($request->user(), $request->all()));
    }

    private function redirectToStatus(Request $request, PaymentFlowResult $result): RedirectResponse
    {
        $intent = $result->payment?->payable;

        if (! $intent instanceof DonationIntent) {
            return to_route('donations.guest.entry')
                ->with('guest_donation_message', $result->message);
        }

        if (in_array($result->status, ['paid', 'manual_review'], true)) {
            $request->session()->forget(DonationCheckoutService::CURRENT_INTENT_SESSION_KEY);
        }

        if ($result->status === 'paid') {
            $request->session()->forget(GuestDonationEntryController::DRAFT_SESSION_KEY);
        }

        return to_route('donations.payments.show', ['publicReference' => $intent->public_reference])
            ->with('donation_status_message', $this->statusMessage($intent, $result))
            ->with('donation_status_variant', $this->statusVariant($result->status));
    }

    private function statusVariant(string $status): string
    {
        return match ($status) {
            'paid' => 'success',
            'failed', 'cancelled', 'manual_review' => 'warning',
            default => 'info',
        };
    }

    private function statusMessage(DonationIntent $intent, PaymentFlowResult $result): string
    {
        if ($result->status !== 'paid') {
            return $result->message;
        }

        return $intent->donor_mode === DonationIntent::DONOR_MODE_GUEST
            ? 'Your donation was settled successfully. No account was created automatically.'
            : 'Your account-linked donation was settled successfully. Donor portal access remains a separate later step.';
    }
}
