{{-- resources/views/reports/expense-report.blade.php --}}
@php
    $pageTitle = 'Expense Report';
    $printedAt = $printedAt ?? now();

    $periodTotal = (float)($periodTotal ?? 0);
    $yearTotal = (float)($yearTotal ?? 0);

    // safe strings
    $view = $view ?? 'monthly';
    $bucket = $bucket ?? 'all';
@endphp

<x-app-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;600;700&display=swap');

        .bn-font {
            font-family: "Noto Sans Bengali", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, "Helvetica Neue", sans-serif;
        }

        /* ✅ this page: hide app nav/header (paper clean) */
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
                        View:
                        <span class="font-semibold text-slate-900">{{ $view === 'yearly' ? 'Yearly' : 'Monthly' }}</span>
                        <span class="text-slate-400">•</span>
                        Range:
                        <span class="font-semibold text-slate-900">{{ $start ?? '-' }} → {{ $end ?? '-' }}</span>

                        @if(($bucket ?? 'all') !== 'all')
                            <span class="text-slate-400">•</span>
                            Bucket:
                            <span class="font-semibold text-slate-900">{{ ucfirst($bucket) }}</span>
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

            {{-- ✅ Filters (screen only) --}}
            <div class="print:hidden bg-white border border-slate-200 rounded-2xl p-4">
                <form method="GET" action="{{ route('reports.expense-report') }}" class="grid grid-cols-12 gap-3">

                    <div class="col-span-12 sm:col-span-2">
                        <label class="block text-xs font-medium text-slate-600 mb-1">View</label>
                        <select name="view"
                            class="w-full rounded-xl border-slate-200 text-sm
                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="monthly" @selected(($view ?? 'monthly') === 'monthly')>Monthly</option>
                            <option value="yearly" @selected(($view ?? 'monthly') === 'yearly')>Yearly</option>
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Month</label>
                        <input type="month" name="month" value="{{ $month ?? now()->format('Y-m') }}"
                            class="w-full rounded-xl border-slate-200 text-sm
                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div class="col-span-12 sm:col-span-2">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Year</label>
                        <input type="number" name="year" value="{{ $year ?? now()->year }}"
                            class="w-full rounded-xl border-slate-200 text-sm
                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Bucket</label>
                        <select name="bucket"
                            class="w-full rounded-xl border-slate-200 text-sm
                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="all" @selected(($bucket ?? 'all') === 'all')>All</option>
                            <option value="general" @selected(($bucket ?? 'all') === 'general')>General</option>
                            <option value="boarding" @selected(($bucket ?? 'all') === 'boarding')>Boarding</option>
                            <option value="construction" @selected(($bucket ?? 'all') === 'construction')>Construction</option>
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-2">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Account</label>
                        <select name="account_id"
                            class="w-full rounded-xl border-slate-200 text-sm
                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All</option>
                            @foreach(($accounts ?? []) as $acc)
                                <option value="{{ $acc->id }}" @selected((string)($accountId ?? '') === (string)$acc->id)>
                                    {{ $acc->name ?? ('Account #'.$acc->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-4">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Category (Specific)</label>
                        <select name="catagory_id"
                            class="w-full rounded-xl border-slate-200 text-sm
                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All Categories</option>
                            @foreach(($categories ?? []) as $c)
                                <option value="{{ $c['id'] }}" @selected((string)($catagoryId ?? '') === (string)$c['id'])>
                                    {{ $c['label'] ?: ('Category #'.$c['id']) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-4">
                        <label class="block text-xs font-medium text-slate-600 mb-1">
                            Expense Head (Optional)
                            @if(empty($headCol))
                                <span class="text-rose-600">(transactions table has no head column)</span>
                            @endif
                        </label>
                        <select name="expens_id"
                            class="w-full rounded-xl border-slate-200 text-sm
                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            @disabled(empty($headCol))>
                            <option value="">All Heads</option>
                            @foreach(($expenseHeads ?? []) as $h)
                                <option value="{{ $h->id }}" @selected((string)($expenseHeadId ?? '') === (string)$h->id)>
                                    {{ $h->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-4 flex items-end">
                        <button
                            class="w-full rounded-xl bg-emerald-600 text-white text-sm px-4 py-2 hover:bg-emerald-700">
                            Apply
                        </button>
                    </div>
                </form>
            </div>

            {{-- ✅ Summary --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-4">
                <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-12 lg:col-span-6">
                        <div class="text-xs text-slate-500">Period</div>
                        <div class="text-sm font-semibold text-slate-900">
                            @if(($view ?? 'monthly') === 'yearly')
                                Year: {{ $year ?? '-' }}
                            @else
                                Month: {{ $monthLabel ?? ($month ?? '-') }}
                            @endif
                        </div>
                        <div class="text-[11px] text-slate-500 mt-1">{{ $start ?? '-' }} → {{ $end ?? '-' }}</div>
                    </div>

                    <div class="col-span-12 lg:col-span-6">
                        <div class="text-xs text-slate-500">Total Expense (Debit)</div>
                        <div class="text-xl font-extrabold text-rose-700">
                            {{ number_format(($view ?? 'monthly') === 'yearly' ? $yearTotal : $periodTotal, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ Yearly view: month-wise table --}}
            @if(($view ?? 'monthly') === 'yearly')
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                        <div class="text-sm font-semibold text-slate-900">Month-wise Expense Summary</div>
                        <div class="text-xs text-slate-500">Year: {{ $year ?? '-' }}</div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead class="bg-slate-50 text-slate-600 uppercase">
                                <tr>
                                    <th class="px-3 py-2 text-left">Month</th>
                                    <th class="px-3 py-2 text-right">Total Expense (Debit)</th>
                                    <th class="px-3 py-2 text-right print:hidden">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach(($months ?? []) as $m)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <div class="font-semibold text-slate-900">{{ $m['label'] }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $m['ym'] }}</div>
                                        </td>
                                        <td class="px-3 py-2 text-right font-semibold text-rose-700">
                                            {{ number_format((float)($m['total'] ?? 0), 2) }}
                                        </td>
                                        <td class="px-3 py-2 text-right print:hidden">
                                            <a class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs hover:bg-slate-50"
                                               href="{{ route('reports.expense-report', array_filter([
                                                    'view' => 'monthly',
                                                    'month' => $m['ym'],
                                                    'year' => $year ?? null,
                                                    'bucket' => ($bucket ?? 'all') !== 'all' ? ($bucket ?? null) : null,
                                                    'account_id' => $accountId ?? null,
                                                    'catagory_id' => $catagoryId ?? null,
                                                    'expens_id' => $expenseHeadId ?? null,
                                               ])) }}">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 border-t border-slate-200">
                                <tr>
                                    <td class="px-3 py-3 text-right font-semibold text-slate-900">Year Total</td>
                                    <td class="px-3 py-3 text-right font-extrabold text-rose-700">
                                        {{ number_format($yearTotal, 2) }}
                                    </td>
                                    <td class="px-3 py-3 print:hidden"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ✅ Monthly view: detailed rows --}}
            @if(($view ?? 'monthly') === 'monthly')
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                        <div class="text-sm font-semibold text-slate-900">Monthly Expense Details</div>
                        <div class="text-xs text-slate-500">Rows: {{ count($rows ?? []) }}</div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead class="bg-slate-50 text-slate-600 uppercase">
                                <tr>
                                    <th class="px-3 py-2 text-left">Date</th>
                                    <th class="px-3 py-2 text-left">Title / Note</th>
                                    <th class="px-3 py-2 text-left">Account</th>
                                    <th class="px-3 py-2 text-right">Amount (Debit)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse(($rows ?? []) as $tx)
                                    @php
                                        $titleText = trim((string)($tx->title ?? $tx->c_s_1 ?? $tx->note ?? '')) ?: '-';
                                        $accName = data_get($tx, 'account.name') ?? ('Account #'.($tx->account_id ?? ''));
                                        $amt = (float)($tx->debit ?? 0);
                                        $catId = $tx->catagory_id ?? null;
                                    @endphp
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <div class="font-semibold text-slate-900">{{ $tx->transactions_date ?? '-' }}</div>
                                            <div class="text-[11px] text-slate-500">#{{ $tx->id }}</div>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="font-semibold text-slate-900">{{ $titleText }}</div>
                                            <div class="text-[11px] text-slate-500">Category ID: {{ $catId ?? '-' }}</div>
                                        </td>
                                        <td class="px-3 py-2">{{ $accName }}</td>
                                        <td class="px-3 py-2 text-right font-semibold text-rose-700">{{ number_format($amt, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-slate-500">No expense found for this month.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-slate-50 border-t border-slate-200">
                                <tr>
                                    <td colspan="3" class="px-3 py-3 text-right font-semibold text-slate-900">Month Total</td>
                                    <td class="px-3 py-3 text-right font-extrabold text-rose-700">{{ number_format($periodTotal, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ✅ Totals (MUST be last) --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200">
                    <div class="text-sm font-semibold text-slate-900">Totals</div>
                    <div class="text-xs text-slate-500">
                        {{ ($view ?? 'monthly') === 'yearly' ? ('Year: '.($year ?? '-')) : ('Month: '.($monthLabel ?? ($month ?? '-'))) }}
                        • {{ $start ?? '-' }} → {{ $end ?? '-' }}
                    </div>
                </div>

                <div class="p-4">
                    <div class="grid grid-cols-12 gap-3 print-block">
                        <div class="col-span-12 border border-slate-200 rounded-2xl p-4 print:rounded-none print:border-0">
                            <div class="text-xs text-slate-500">Total Expense (Debit)</div>
                            <div class="text-2xl font-extrabold text-rose-700">
                                {{ number_format(($view ?? 'monthly') === 'yearly' ? $yearTotal : $periodTotal, 2) }}
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