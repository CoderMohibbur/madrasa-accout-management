{{-- resources/views/reports/monthly-statement.blade.php --}}
@php
    $pageTitle = 'Monthly Statement';
    $printedAt = $printedAt ?? now();

    $totalIncome = (float)($totalIncome ?? 0);
    $totalExpense = (float)($totalExpense ?? 0);
    $surplus = (float)($surplus ?? ($totalIncome - $totalExpense));
@endphp

<x-app-layout>
    {{-- ❌ app layout header slot ব্যবহার করছি না (print page clean রাখতে) --}}

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;600;700&display=swap');

        .bn-font {
            font-family: "Noto Sans Bengali", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, "Helvetica Neue", sans-serif;
        }

        /* ✅ এই পেজে app topbar/nav hide */
        body>div.min-h-screen>nav,
        body>div.min-h-screen>header {
            display: none !important;
        }

        @page {
            size: A4;
            margin: 12mm;
        }

        @media print {
            .print\:hidden {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            body>div.min-h-screen>nav,
            body>div.min-h-screen>header {
                display: none !important;
            }

            a[href]:after {
                content: "";
            }

            /* ✅ totals grid => row-wise stack */
            .print-block {
                display: block !important;
            }
        }

        /* page-break safe tables */
        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }
    </style>

    <div class="py-6 bn-font">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- ✅ Paper Header --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="text-lg font-extrabold text-slate-900 leading-6">
                                আত-তাওহীদ ইসলামী কমপ্লেক্স
                            </div>
                            <div class="text-xs text-slate-600 mt-1">
                                কৃষ্ণবাটি, পুলেরহাট, সদর যশোর • +880 1772-088881 • attawheedic@gmail.com
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1">
                                Website: https://attawheedic.com
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <div class="text-xs text-slate-500">Report</div>
                            <div class="text-sm font-semibold text-slate-900">{{ $pageTitle }}</div>
                            <div class="text-[11px] text-slate-500 mt-1">
                                Printed: {{ $printedAt->format('Y-m-d h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions (screen only) --}}
                <div class="print:hidden px-5 py-3 bg-slate-50/70 flex items-center justify-between gap-3">
                    <div class="text-xs text-slate-600">
                        Month:
                        <span class="font-semibold text-slate-900">{{ $monthLabel ?? $month }}</span>
                        <span class="text-slate-400">•</span>
                        Range:
                        <span class="font-semibold text-slate-900">{{ $start }} → {{ $end }}</span>
                        @if ($accountId)
                            <span class="text-slate-400">•</span>
                            <span class="text-slate-600">Account filtered</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="window.print()"
                            class="inline-flex items-center rounded-xl bg-emerald-600 px-3 py-2 text-sm font-semibold text-white
                                   hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            Print / Save PDF
                        </button>
                    </div>
                </div>
            </div>

            {{-- Filters (screen only) --}}
            <div class="print:hidden bg-white border border-slate-200 rounded-2xl p-4">
                <form method="GET" action="{{ route('reports.monthly-statement') }}" class="grid grid-cols-12 gap-3">
                    <div class="col-span-12 sm:col-span-4">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Month</label>
                        <input type="month" name="month" value="{{ $month }}"
                            class="w-full rounded-xl border-slate-200 text-sm
                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div class="col-span-12 sm:col-span-6">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Account (Optional)</label>
                        <select name="account_id"
                            class="w-full rounded-xl border-slate-200 text-sm
                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All Accounts</option>
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}" @selected((string) $accountId === (string) $acc->id)>
                                    {{ $acc->name ?? ('Account #' . $acc->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-2 flex items-end">
                        <button type="submit"
                            class="w-full rounded-xl bg-emerald-600 text-white text-sm px-4 py-2 hover:bg-emerald-700">
                            Apply
                        </button>
                    </div>
                </form>
            </div>

            {{-- Summary (print friendly) --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-4">
                <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-12 lg:col-span-6">
                        <div class="text-xs text-slate-500">Statement Month</div>
                        <div class="text-sm font-semibold text-slate-900">{{ $monthLabel ?? $month }}</div>
                        <div class="text-[11px] text-slate-500 mt-1">
                            {{ $start }} → {{ $end }}
                            @if ($accountId)
                                • Account filtered
                            @endif
                        </div>
                    </div>

                    <div class="col-span-12 sm:col-span-4 lg:col-span-3">
                        <div class="text-xs text-slate-500">Total Income (Credit)</div>
                        <div class="text-lg font-extrabold text-emerald-700">{{ number_format($totalIncome, 2) }}</div>
                    </div>

                    <div class="col-span-12 sm:col-span-4 lg:col-span-3">
                        <div class="text-xs text-slate-500">Total Expense (Debit)</div>
                        <div class="text-lg font-extrabold text-rose-700">{{ number_format($totalExpense, 2) }}</div>
                    </div>
                </div>
            </div>

            {{-- Two columns: Income left / Expense right --}}
            <div class="grid grid-cols-12 gap-4">
                {{-- ✅ Income --}}
                <div class="col-span-12 lg:col-span-6">
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                            <div class="text-sm font-semibold text-slate-900">Income (Credit)</div>
                            <div class="text-sm font-extrabold text-emerald-700">{{ number_format($totalIncome, 2) }}</div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead class="bg-slate-50 text-slate-600 uppercase">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Date</th>
                                        <th class="px-3 py-2 text-left">Details</th>
                                        <th class="px-3 py-2 text-right">Amount</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100">
                                    @forelse($incomeTx as $tx)
                                        @php
                                            $amt = (float) ($tx->credit ?? 0);
                                            $disp = $getDisplay($tx);
                                            $typeName = data_get($tx, 'type.name') ?? ('Type #'.($tx->transactions_type_id ?? ''));
                                            $note = trim((string) ($tx->note ?? ($tx->remarks ?? '')));
                                        @endphp
                                        <tr>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <div class="font-semibold text-slate-900">{{ $tx->transactions_date ?? '-' }}</div>
                                                <div class="text-[11px] text-slate-500">#{{ $tx->id }}</div>
                                            </td>

                                            <td class="px-3 py-2">
                                                <div class="font-semibold text-slate-900">{{ $typeName }}</div>
                                                <div class="text-[11px] text-slate-600 mt-0.5">
                                                    @if ($disp['party_name'])
                                                        <span class="font-semibold">{{ $disp['party_label'] }}:</span>
                                                        {{ $disp['party_name'] }}
                                                    @endif
                                                    @if ($disp['title'])
                                                        <span class="text-slate-400"> • </span>{{ $disp['title'] }}
                                                    @endif
                                                </div>
                                                @if ($note !== '')
                                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $note }}</div>
                                                @endif
                                            </td>

                                            <td class="px-3 py-2 text-right font-semibold text-emerald-700 whitespace-nowrap">
                                                {{ number_format($amt, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-10 text-center text-slate-500">No income found for this month.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ✅ Expense --}}
                <div class="col-span-12 lg:col-span-6">
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                            <div class="text-sm font-semibold text-slate-900">Expense (Debit)</div>
                            <div class="text-sm font-extrabold text-rose-700">{{ number_format($totalExpense, 2) }}</div>
                        </div>

                        <div class="p-4 space-y-4">
                            @forelse($expenseGroups as $bucket => $items)
                                @php
                                    $bucketTotal = (float) $items->sum(fn($t) => (float) ($t->debit ?? 0));
                                @endphp

                                <div class="border border-slate-200 rounded-2xl overflow-hidden">
                                    <div class="px-3 py-2 bg-slate-50 flex items-center justify-between">
                                        <div class="text-xs font-semibold text-slate-700">{{ $bucket }}</div>
                                        <div class="text-xs font-extrabold text-rose-700">{{ number_format($bucketTotal, 2) }}</div>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-xs">
                                            <thead class="bg-white text-slate-600 uppercase border-t border-slate-200">
                                                <tr>
                                                    <th class="px-3 py-2 text-left">Date</th>
                                                    <th class="px-3 py-2 text-left">Details</th>
                                                    <th class="px-3 py-2 text-right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                @foreach ($items as $tx)
                                                    @php
                                                        $amt = (float) ($tx->debit ?? 0);
                                                        $disp = $getDisplay($tx);
                                                        $typeName = data_get($tx, 'type.name') ?? ('Type #'.($tx->transactions_type_id ?? ''));
                                                        $note = trim((string) ($tx->note ?? ($tx->remarks ?? '')));
                                                    @endphp
                                                    <tr>
                                                        <td class="px-3 py-2 whitespace-nowrap">
                                                            <div class="font-semibold text-slate-900">{{ $tx->transactions_date ?? '-' }}</div>
                                                            <div class="text-[11px] text-slate-500">#{{ $tx->id }}</div>
                                                        </td>

                                                        <td class="px-3 py-2">
                                                            <div class="font-semibold text-slate-900">{{ $typeName }}</div>
                                                            <div class="text-[11px] text-slate-600 mt-0.5">
                                                                @if ($disp['party_name'])
                                                                    <span class="font-semibold">{{ $disp['party_label'] }}:</span>
                                                                    {{ $disp['party_name'] }}
                                                                @endif
                                                                @if ($disp['title'])
                                                                    <span class="text-slate-400"> • </span>{{ $disp['title'] }}
                                                                @endif
                                                            </div>
                                                            @if ($note !== '')
                                                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $note }}</div>
                                                            @endif
                                                        </td>

                                                        <td class="px-3 py-2 text-right font-semibold text-rose-700 whitespace-nowrap">
                                                            {{ number_format($amt, 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @empty
                                <div class="border border-slate-200 rounded-2xl p-8 text-center text-slate-500">
                                    No expense found for this month.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ Totals (MUST be last) — screen grid, print row-wise --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200">
                    <div class="text-sm font-semibold text-slate-900">Monthly Totals</div>
                    <div class="text-xs text-slate-500">
                        {{ $monthLabel ?? $month }} • {{ $start }} → {{ $end }}
                        @if ($accountId) • Account filtered @endif
                    </div>
                </div>

                <div class="p-4">
                    <div class="grid grid-cols-12 gap-3 print-block">
                        <div class="col-span-12 lg:col-span-4 border border-slate-200 rounded-2xl p-4 print:rounded-none print:border-0 print:border-b">
                            <div class="text-xs text-slate-500">Total Income (Credit)</div>
                            <div class="text-xl font-extrabold text-emerald-700">{{ number_format($totalIncome, 2) }}</div>
                        </div>

                        <div class="col-span-12 lg:col-span-4 border border-slate-200 rounded-2xl p-4 print:rounded-none print:border-0 print:border-b">
                            <div class="text-xs text-slate-500">Total Expense (Debit)</div>
                            <div class="text-xl font-extrabold text-rose-700">{{ number_format($totalExpense, 2) }}</div>
                        </div>

                        <div class="col-span-12 lg:col-span-4 border border-slate-200 rounded-2xl p-4 print:rounded-none print:border-0">
                            <div class="text-xs text-slate-500">Surplus / Deficit (Credit − Debit)</div>
                            <div class="text-xl font-extrabold {{ $surplus >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ number_format($surplus, 2) }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 text-center text-[11px] text-slate-500">
                        This report is generated from Madrasha Account Management • https://attawheedic.com
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>