<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use App\Services\DonorPortal\DonorPortalData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DonorPortalController extends Controller
{
    public function __construct(
        private readonly DonorPortalData $donorPortalData,
    ) {
    }

    public function index(Request $request): View
    {
        $access = $this->donorPortalData->requireDonorAccess($request->user());

        if ($access->requiresNoPortalView()) {
            return view('donor.no-portal', [
                'access' => $access,
                'donor' => $access->donor,
            ]);
        }

        $donor = $access->donor;
        $donations = $this->donorPortalData->donationHistoryItems($donor);
        $receipts = $this->donorPortalData->receiptHistoryItems($donor);

        $summary = $this->buildDonationSummary($donations) + [
            'receipt_count' => $receipts->count(),
        ];
        $recentDonations = $donations->take(5);
        $recentReceipts = $receipts->take(5);

        return view('donor.dashboard', compact(
            'donor',
            'summary',
            'recentDonations',
            'recentReceipts',
        ));
    }

    public function donations(Request $request): View|RedirectResponse
    {
        $access = $this->donorPortalData->requireDonorAccess($request->user());

        if ($access->requiresNoPortalView()) {
            return $this->redirectToDonorHomeWithMessage('Donor portal history is not enabled for this account yet.');
        }

        $donor = $access->donor;
        $donationItems = $this->donorPortalData->donationHistoryItems($donor);
        $summary = $this->buildDonationSummary($donationItems);
        $donations = $this->donorPortalData->paginateItems($donationItems, 10)->withQueryString();

        return view('donor.donations.index', compact(
            'donor',
            'summary',
            'donations',
        ));
    }

    public function receipts(Request $request): View|RedirectResponse
    {
        $access = $this->donorPortalData->requireDonorAccess($request->user());

        if ($access->requiresNoPortalView()) {
            return $this->redirectToDonorHomeWithMessage('Donor receipt history stays limited to portal-eligible accounts.');
        }

        $donor = $access->donor;
        $receiptItems = $this->donorPortalData->receiptHistoryItems($donor);
        $summary = [
            'receipt_count' => $receiptItems->count(),
            'receipt_total' => (float) $receiptItems->sum('amount'),
            'latest_receipt_at' => $receiptItems->first()['issued_at'] ?? null,
        ];
        $receipts = $this->donorPortalData->paginateItems($receiptItems, 10)->withQueryString();

        return view('donor.receipts.index', compact(
            'donor',
            'summary',
            'receipts',
        ));
    }

    private function buildDonationSummary(Collection $donations): array
    {
        return [
            'donation_count' => $donations->count(),
            'donation_total' => (float) $donations->sum('amount'),
            'latest_donation_at' => $donations->first()['occurred_at'] ?? null,
        ];
    }

    private function redirectToDonorHomeWithMessage(string $message): RedirectResponse
    {
        return redirect()
            ->route('donor.dashboard')
            ->with('error', $message);
    }
}
