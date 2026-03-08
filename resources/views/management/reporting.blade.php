@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Safe Management Reporting</h2>
                <p class="mt-1 text-sm text-slate-500">
                    This additive reporting page uses the newer guardian, donor, invoice, payment, and receipt boundaries.
                    The legacy <code>reports.*</code> pages remain unchanged.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('management.access-control') }}"
                    class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    Access Control
                </a>
                <a href="{{ route('reports.transactions') }}"
                    class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    Legacy Transactions Report
                </a>
                <a href="{{ route('reports.monthly-statement') }}"
                    class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    Monthly Statement
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                This page is additive. It does not replace or reinterpret the existing report controllers; it gives management
                a safer view into fee invoices, donor inflows, and receipts using the newer domain boundaries.
            </div>

            <form method="GET" action="{{ route('management.reporting.index') }}"
                class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-[0.16em] text-slate-500">From</label>
                        <input type="date" name="from" value="{{ $filters['from'] }}"
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-[0.16em] text-slate-500">To</label>
                        <input type="date" name="to" value="{{ $filters['to'] }}"
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div class="flex items-end gap-2 md:col-span-2">
                        <button
                            class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Apply Range
                        </button>
                        <a href="{{ route('management.reporting.index') }}"
                            class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="text-xs uppercase tracking-[0.18em] text-slate-500">Total Inflow</div>
                    <div class="mt-3 text-3xl font-semibold text-slate-900">{{ $money($summary['total_inflow']) }}</div>
                </div>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                    <div class="text-xs uppercase tracking-[0.18em] text-emerald-700">Student Fees</div>
                    <div class="mt-3 text-3xl font-semibold text-emerald-900">{{ $money($summary['student_fee_total']) }}</div>
                </div>
                <div class="rounded-2xl border border-sky-200 bg-sky-50 p-5 shadow-sm">
                    <div class="text-xs uppercase tracking-[0.18em] text-sky-700">Donations</div>
                    <div class="mt-3 text-3xl font-semibold text-sky-900">{{ $money($summary['donation_total']) }}</div>
                </div>
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                    <div class="text-xs uppercase tracking-[0.18em] text-amber-700">Outstanding Invoices</div>
                    <div class="mt-3 text-3xl font-semibold text-amber-900">{{ $money($summary['outstanding_invoice_total']) }}</div>
                </div>
                <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5 shadow-sm">
                    <div class="text-xs uppercase tracking-[0.18em] text-violet-700">Receipts Issued</div>
                    <div class="mt-3 text-3xl font-semibold text-violet-900">{{ $money($summary['receipt_total']) }}</div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[1.1fr,0.9fr]">
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-slate-900">Source-Wise Inflow Breakdown</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Inflows are shown separately so fees and donations are no longer collapsed into a single total.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                                <tr>
                                    <th class="px-6 py-4">Source</th>
                                    <th class="px-6 py-4">Rows</th>
                                    <th class="px-6 py-4">Total</th>
                                    <th class="px-6 py-4">Share</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($inflowBreakdown as $source)
                                    <tr class="text-slate-700">
                                        <td class="px-6 py-4 font-medium text-slate-900">{{ $source['label'] }}</td>
                                        <td class="px-6 py-4">{{ $source['rows'] }}</td>
                                        <td class="px-6 py-4">{{ $money($source['total']) }}</td>
                                        <td class="px-6 py-4">{{ number_format((float) $source['share'], 2) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Student Fee Status</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Invoice visibility comes from the additive Phase 1 billing tables, not from inferred legacy report totals.
                    </p>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs uppercase tracking-[0.16em] text-slate-500">Invoices</div>
                            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $invoiceSummary['invoice_count'] }}</div>
                        </div>
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                            <div class="text-xs uppercase tracking-[0.16em] text-emerald-700">Paid</div>
                            <div class="mt-2 text-2xl font-semibold text-emerald-900">{{ $invoiceSummary['paid_count'] }}</div>
                        </div>
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                            <div class="text-xs uppercase tracking-[0.16em] text-amber-700">Open</div>
                            <div class="mt-2 text-2xl font-semibold text-amber-900">{{ $invoiceSummary['open_count'] }}</div>
                        </div>
                        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                            <div class="text-xs uppercase tracking-[0.16em] text-rose-700">Overdue</div>
                            <div class="mt-2 text-2xl font-semibold text-rose-900">{{ $invoiceSummary['overdue_count'] }}</div>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <div class="text-xs uppercase tracking-[0.16em] text-slate-500">Billed Total</div>
                            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $money($invoiceSummary['billed_total']) }}</div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <div class="text-xs uppercase tracking-[0.16em] text-slate-500">Outstanding Balance</div>
                            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $money($invoiceSummary['outstanding_total']) }}</div>
                        </div>
                    </div>

                    <div class="mt-5 rounded-2xl border border-violet-200 bg-violet-50 p-4 text-sm text-violet-900">
                        Receipts in this range: <strong>{{ $receiptSummary['receipt_count'] }}</strong>
                    </div>
                </section>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-slate-900">Open Student Invoices</h3>
                        <p class="mt-1 text-sm text-slate-500">Only invoices with remaining balance in the selected range appear here.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                                <tr>
                                    <th class="px-6 py-4">Invoice</th>
                                    <th class="px-6 py-4">Student</th>
                                    <th class="px-6 py-4">Due</th>
                                    <th class="px-6 py-4">Balance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($openInvoices as $invoice)
                                    <tr class="text-slate-700">
                                        <td class="px-6 py-4 font-medium text-slate-900">{{ $invoice->invoice_number }}</td>
                                        <td class="px-6 py-4">{{ $invoice->student?->full_name ?: 'Student #'.$invoice->student_id }}</td>
                                        <td class="px-6 py-4">{{ optional($invoice->due_at)->format('Y-m-d') ?: '-' }}</td>
                                        <td class="px-6 py-4">{{ $money($invoice->balance_amount) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-500">
                                            No open invoices were issued in this range.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-slate-900">Recent Receipts</h3>
                        <p class="mt-1 text-sm text-slate-500">Receipt visibility comes from explicit receipt records, not reconstructed legacy transaction data.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                                <tr>
                                    <th class="px-6 py-4">Receipt</th>
                                    <th class="px-6 py-4">Provider</th>
                                    <th class="px-6 py-4">Amount</th>
                                    <th class="px-6 py-4">Issued</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($recentReceipts as $receipt)
                                    <tr class="text-slate-700">
                                        <td class="px-6 py-4 font-medium text-slate-900">{{ $receipt->receipt_number }}</td>
                                        <td class="px-6 py-4">{{ $receipt->payment?->provider ?: 'manual' }}</td>
                                        <td class="px-6 py-4">{{ $money($receipt->amount) }} {{ $receipt->currency ?: 'BDT' }}</td>
                                        <td class="px-6 py-4">{{ optional($receipt->issued_at ?? $receipt->created_at)->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-500">
                                            No receipts were issued in this range.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
