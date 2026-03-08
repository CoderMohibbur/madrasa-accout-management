<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use App\Services\DonorPortal\DonorPortalData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DonorPortalController extends Controller
{
    public function __construct(
        private readonly DonorPortalData $donorPortalData,
    ) {
    }

    public function index(Request $request): View
    {
        $donor = $this->donorPortalData->requireDonor($request->user());

        $donationQuery = $this->donorPortalData->donationQuery($donor);
        $receiptQuery = $this->donorPortalData->receiptQuery($donor);

        $summary = [
            'donation_count' => (clone $donationQuery)->count(),
            'donation_total' => (float) (clone $donationQuery)->sum('credit'),
            'latest_donation_at' => (clone $donationQuery)->max('transactions_date'),
            'receipt_count' => (clone $receiptQuery)->count(),
        ];

        $recentDonations = (clone $donationQuery)
            ->orderByDesc('transactions_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $recentReceipts = (clone $receiptQuery)
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('donor.dashboard', compact(
            'donor',
            'summary',
            'recentDonations',
            'recentReceipts',
        ));
    }

    public function donations(Request $request): View
    {
        $donor = $this->donorPortalData->requireDonor($request->user());
        $donationQuery = $this->donorPortalData->donationQuery($donor);

        $summary = [
            'donation_count' => (clone $donationQuery)->count(),
            'donation_total' => (float) (clone $donationQuery)->sum('credit'),
            'latest_donation_at' => (clone $donationQuery)->max('transactions_date'),
        ];

        $donations = $donationQuery
            ->orderByDesc('transactions_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('donor.donations.index', compact(
            'donor',
            'summary',
            'donations',
        ));
    }

    public function receipts(Request $request): View
    {
        $donor = $this->donorPortalData->requireDonor($request->user());
        $receiptQuery = $this->donorPortalData->receiptQuery($donor);

        $summary = [
            'receipt_count' => (clone $receiptQuery)->count(),
            'receipt_total' => (float) (clone $receiptQuery)->sum('amount'),
            'latest_receipt_at' => (clone $receiptQuery)->max('issued_at'),
        ];

        $receipts = $receiptQuery
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('donor.receipts.index', compact(
            'donor',
            'summary',
            'receipts',
        ));
    }
}
