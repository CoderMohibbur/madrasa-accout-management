{{-- resources/views/reports/yearly-summary.blade.php --}}
@php
    $pageTitle = 'Yearly Summary';
    $printedAt = $printedAt ?? now();
    $yearIncome = (float)($yearIncome ?? 0);
    $yearExpense = (float)($yearExpense ?? 0);
    $yearSurplus = $yearIncome - $yearExpense;
@endphp

<x-app-layout>
    {{-- ❌ app layout header slot ব্যবহার করছি না (print clean রাখতে) --}}

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
                        Year: <span class="font-semibold text-slate-900">{{ $year }}</span>
                        @if ($accountId)
                            <span class="text-slate-400">•</span>
                            <span class="text-slate-600">Account filtered</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('reports.monthly-statement') }}"
                            class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700
                                  hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            Monthly Statement
                        </a>

                        <button type="button" onclick="window.print()"
                            class="inline-flex items-center rounded-xl bg-emerald-600 px-3 py-2 text-sm font-semibold text-white
                                   hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            Print / Save PDF
                        </button>
                    </div>
                </div>
            </div>

            {{-- Filters (screen only) --}}
            <form method="GET" action="{{ route('reports.yearly-summary') }}"
                class="print:hidden bg-white border border-slate-200 rounded-2xl p-4">
                <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Year</label>
                        <input type="number" name="year" value="{{ $year }}"
                            class="w-full rounded-xl border-slate-200 text-sm
                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div class="col-span-12 sm:col-span-7">
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
                        <button
                            class="w-full rounded-xl bg-emerald-600 text-white text-sm px-4 py-2 hover:bg-emerald-700">
                            Apply
                        </button>
                    </div>
                </div>
            </form>

            {{-- ✅ Screen view: Month cards (clickable) --}}
            <div class="print:hidden grid grid-cols-12 gap-3">
                @foreach ($months as $m)
                    <a href="{{ route('reports.monthly-statement', array_filter([
                        'month' => $m['ym'],
                        'account_id' => $accountId,
                    ])) }}"
                        class="col-span-12 sm:col-span-6 lg:col-span-3 bg-white border border-slate-200 rounded-2xl p-4 hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-semibold text-slate-900">{{ $m['label'] }}</div>
                            <div class="text-[11px] text-slate-500">{{ $m['ym'] }}</div>
                        </div>

                        <div class="mt-3 text-xs text-slate-500">Income (Credit)</div>
                        <div class="text-lg font-extrabold text-emerald-700">{{ number_format((float) $m['income'], 2) }}</div>

                        <div class="mt-2 text-xs text-slate-500">Expense (Debit)</div>
                        <div class="text-lg font-extrabold text-rose-700">{{ number_format((float) $m['expense'], 2) }}</div>

                        <div class="mt-2 text-xs text-slate-500">Surplus</div>
                        <div class="text-sm font-bold {{ $m['surplus'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ number_format((float) $m['surplus'], 2) }}
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- ✅ Print view: Table (paper-perfect) --}}
            <div class="hidden print:block bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                    <div class="text-sm font-semibold text-slate-900">Month-wise Summary</div>
                    <div class="text-xs text-slate-500">Year: {{ $year }}</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead class="bg-slate-50 text-slate-600 uppercase">
                            <tr>
                                <th class="px-3 py-2 text-left">Month</th>
                                <th class="px-3 py-2 text-right">Income (Credit)</th>
                                <th class="px-3 py-2 text-right">Expense (Debit)</th>
                                <th class="px-3 py-2 text-right">Surplus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($months as $m)
                                <tr>
                                    <td class="px-3 py-2">
                                        <div class="font-semibold text-slate-900">{{ $m['label'] }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $m['ym'] }}</div>
                                    </td>
                                    <td class="px-3 py-2 text-right font-semibold text-emerald-700">
                                        {{ number_format((float) $m['income'], 2) }}
                                    </td>
                                    <td class="px-3 py-2 text-right font-semibold text-rose-700">
                                        {{ number_format((float) $m['expense'], 2) }}
                                    </td>
                                    <td class="px-3 py-2 text-right font-semibold {{ $m['surplus'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                        {{ number_format((float) $m['surplus'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot class="bg-slate-50 border-t border-slate-200">
                            <tr>
                                <td class="px-3 py-3 text-right font-semibold text-slate-900">Year Total</td>
                                <td class="px-3 py-3 text-right font-extrabold text-emerald-700">{{ number_format($yearIncome, 2) }}</td>
                                <td class="px-3 py-3 text-right font-extrabold text-rose-700">{{ number_format($yearExpense, 2) }}</td>
                                <td class="px-3 py-3 text-right font-extrabold {{ $yearSurplus >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ number_format($yearSurplus, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- ✅ Totals (MUST be last) — screen grid, print row-wise --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200">
                    <div class="text-sm font-semibold text-slate-900">Year Summary (Totals)</div>
                    <div class="text-xs text-slate-500">Year: {{ $year }}</div>
                </div>

                <div class="p-4">
                    <div class="grid grid-cols-12 gap-3 print-block">
                        <div class="col-span-12 lg:col-span-4 border border-slate-200 rounded-2xl p-4 print:rounded-none print:border-0 print:border-b">
                            <div class="text-xs text-slate-500">Year Total Income (Credit)</div>
                            <div class="text-xl font-extrabold text-emerald-700">{{ number_format($yearIncome, 2) }}</div>
                        </div>

                        <div class="col-span-12 lg:col-span-4 border border-slate-200 rounded-2xl p-4 print:rounded-none print:border-0 print:border-b">
                            <div class="text-xs text-slate-500">Year Total Expense (Debit)</div>
                            <div class="text-xl font-extrabold text-rose-700">{{ number_format($yearExpense, 2) }}</div>
                        </div>

                        <div class="col-span-12 lg:col-span-4 border border-slate-200 rounded-2xl p-4 print:rounded-none print:border-0">
                            <div class="text-xs text-slate-500">Year Surplus / Deficit</div>
                            <div class="text-xl font-extrabold {{ $yearSurplus >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ number_format($yearSurplus, 2) }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 text-center text-[11px] text-slate-500">
                        This report is generated from Madrasha Account Management
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>