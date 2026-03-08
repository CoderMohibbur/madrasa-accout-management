<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Services\ManagementReporting\ManagementReportingData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ManagementReportingController extends Controller
{
    public function __construct(
        private readonly ManagementReportingData $managementReportingData,
    ) {
    }

    public function index(Request $request): View
    {
        $range = $this->managementReportingData->resolveRange(
            $request->string('from')->toString(),
            $request->string('to')->toString(),
        );

        $inflowBreakdown = $this->managementReportingData->inflowBreakdown($range['from'], $range['to']);
        $invoiceSummary = $this->managementReportingData->invoiceSummary($range['from'], $range['to']);
        $receiptSummary = $this->managementReportingData->receiptSummary($range['from_at'], $range['to_at']);

        $summary = [
            'total_inflow' => (float) $inflowBreakdown->sum('total'),
            'student_fee_total' => (float) ($inflowBreakdown->firstWhere('key', 'student_fee')['total'] ?? 0),
            'donation_total' => (float) ($inflowBreakdown->firstWhere('key', 'donation')['total'] ?? 0),
            'outstanding_invoice_total' => (float) $invoiceSummary['outstanding_total'],
            'receipt_total' => (float) $receiptSummary['receipt_total'],
        ];

        $openInvoices = $this->managementReportingData->openInvoices($range['from'], $range['to']);
        $recentReceipts = $this->managementReportingData->recentReceipts($range['from_at'], $range['to_at']);

        return view('management.reporting', [
            'filters' => [
                'from' => $range['from'],
                'to' => $range['to'],
            ],
            'summary' => $summary,
            'inflowBreakdown' => $inflowBreakdown,
            'invoiceSummary' => $invoiceSummary,
            'receiptSummary' => $receiptSummary,
            'openInvoices' => $openInvoices,
            'recentReceipts' => $recentReceipts,
        ]);
    }
}
