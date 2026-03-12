<?php

namespace App\Http\Controllers\Donations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Donations\StartGuestDonationRequest;
use App\Models\DonationCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestDonationEntryController extends Controller
{
    public const DRAFT_SESSION_KEY = 'donations.guest.entry_draft';

    public function show(Request $request): View
    {
        return view('donations.guest-entry', [
            'categories' => DonationCategory::query()
                ->active()
                ->ordered()
                ->get()
                ->map(fn (DonationCategory $category): array => $category->toPublicFormOption())
                ->all(),
            'draft' => $request->session()->get(self::DRAFT_SESSION_KEY),
        ]);
    }

    public function start(StartGuestDonationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $category = DonationCategory::query()
            ->active()
            ->where('key', (string) $validated['category'])
            ->firstOrFail();

        $request->session()->forget('donations.current_intent_public_reference');

        $request->session()->put(self::DRAFT_SESSION_KEY, [
            'category_id' => $category->getKey(),
            'category_key' => $category->key,
            'category_label' => $category->displayLabel(),
            'amount' => round((float) $validated['amount'], 2),
            'name' => $validated['name'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'anonymous_display' => (bool) ($validated['anonymous_display'] ?? false),
            'identity_mode' => 'guest',
            'entry_context' => $request->user() ? 'authenticated_session' : 'public_session',
            'captured_at' => now()->toIso8601String(),
        ]);

        $accessKeys = $request->session()->get('donations.access_keys', []);

        if (is_array($accessKeys) && $accessKeys !== []) {
            $request->session()->put('donations.access_keys', []);
        }

        if (! empty($validated['checkout_now'])) {
            return redirect()->route('donations.checkout.start', [], 307);
        }

        return to_route('donations.guest.entry')
            ->with(
                'guest_donation_message',
                'অনুদানের পরিমাণ নিরাপদে সংরক্ষিত হয়েছে। প্রস্তুত হলে এগিয়ে যান।'
            );
    }
}
