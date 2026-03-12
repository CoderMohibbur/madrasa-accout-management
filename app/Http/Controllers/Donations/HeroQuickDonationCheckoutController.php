<?php

namespace App\Http\Controllers\Donations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Donations\HeroQuickDonationRequest;
use App\Models\DonationCategory;
use App\Services\Donations\DonationCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class HeroQuickDonationCheckoutController extends Controller
{
    public function __construct(
        private readonly DonationCheckoutService $donationCheckoutService,
    ) {
    }

    public function store(HeroQuickDonationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $category = DonationCategory::query()
            ->active()
            ->where('key', (string) $validated['fund'])
            ->first();

        if (! $category) {
            throw ValidationException::withMessages([
                'fund' => ['Select a valid donation fund before continuing to checkout.'],
            ]);
        }

        $request->session()->forget(DonationCheckoutService::CURRENT_INTENT_SESSION_KEY);

        $accessKeys = $request->session()->get(DonationCheckoutService::ACCESS_KEYS_SESSION_KEY, []);

        if (is_array($accessKeys) && $accessKeys !== []) {
            $request->session()->put(DonationCheckoutService::ACCESS_KEYS_SESSION_KEY, []);
        }

        $draft = [
            'category_id' => $category->getKey(),
            'category_key' => $category->key,
            'category_label' => $category->displayLabel(),
            'fund_key' => $category->key,
            'fund_label' => $category->displayLabel(),
            'amount' => round((float) $validated['amount'], 2),
            'name' => null,
            'email' => null,
            'phone' => $validated['phone'],
            'anonymous_display' => false,
            'identity_mode' => $request->user() ? 'identified' : 'guest',
            'entry_context' => $request->user() ? 'authenticated_session' : 'public_session',
            'captured_at' => now()->toIso8601String(),
        ];

        $request->session()->put(GuestDonationEntryController::DRAFT_SESSION_KEY, $draft);

        $result = $this->donationCheckoutService->beginCheckout($request, $draft);

        return redirect()->away($result['checkout_url']);
    }
}
