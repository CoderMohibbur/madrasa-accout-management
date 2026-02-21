@php
    $pageTitle = 'Transactions Print / PDF';

    // same party resolver style
    $partyOf = function ($tx) {
        $studentName =
            data_get($tx, 'student.full_name') ??
            (data_get($tx, 'student.name') ?? data_get($tx, 'student.student_name'));

        $donorName =
            data_get($tx, 'donor.name') ?? (data_get($tx, 'donor.donor_name') ?? data_get($tx, 'donor.doner_name'));

        $lenderName = data_get($tx, 'lender.name') ?? data_get($tx, 'lender.lender_name');

        $label = $studentName ? 'Student' : ($donorName ? 'Donor' : ($lenderName ? 'Lender' : '-'));
        $name = $studentName ?: ($donorName ?: ($lenderName ?: '-'));

        return [$label, $name];
    };

    $titleOf = function ($tx) {
        $title = trim((string) ($tx->c_s_1 ?? ''));
        $note = trim((string) ($tx->note ?? ''));
        if ($title === '' && $note !== '') {
            $title = $note;
            $note = '';
        }
        return [$title ?: '-', $note];
    };

    $isPdfRoute = request()->routeIs('reports.transactions.pdf');
@endphp

<x-app-layout>
    {{-- ❌ app layout header slot ব্যবহার করছি না, যাতে টপবার/হেডার না আসে --}}

    {{-- ✅ Bangla font (browser print safe) --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;600;700&display=swap');

        .bn-font {
            font-family: "Noto Sans Bengali", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, "Helvetica Neue", sans-serif;
        }

        /* ✅ আপনার ছবির লাল মার্ক করা app topbar/nav area hide (এই পেজে) */
        body > div.min-h-screen > nav,
        body > div.min-h-screen > header {
            display: none !important;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        @media print {
            .print\:hidden {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            a[href]:after {
                content: "";
            }

            /* print এও নিশ্চিতভাবে app nav/header hide */
            body > div.min-h-screen > nav,
            body > div.min-h-screen > header {
                display: none !important;
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

            {{-- ✅ Website style Paper Header (attawheedic.com feel) --}}
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
                                Printed: {{ now()->format('Y-m-d h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top actions (screen only) --}}
                <div class="print:hidden px-5 py-3 bg-slate-50/70 flex items-center justify-between gap-3">
                    <div class="text-xs text-slate-600">
                        Range: <span class="font-semibold text-slate-900">{{ $from }} → {{ $to }}</span>
                        @if ($month)
                            • Month: <span class="font-semibold text-slate-900">{{ $month }}</span>
                        @endif
                        @if ($year)
                            • Year: <span class="font-semibold text-slate-900">{{ $year }}</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('reports.transactions', request()->query()) }}"
                            class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700
                                   hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            Back to Report
                        </a>

                        <button type="button" onclick="window.print()"
                            class="inline-flex items-center rounded-xl bg-emerald-600 px-3 py-2 text-sm font-semibold text-white
                                   hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            Print / Save PDF
                        </button>
                    </div>
                </div>
            </div>

            {{-- Summary --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-4">
                <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-12 sm:col-span-6">
                        <div class="text-xs text-slate-500">Range</div>
                        <div class="text-sm font-semibold text-slate-800">{{ $from }} → {{ $to }}</div>
                    </div>
                    <div class="col-span-12 sm:col-span-3">
                        <div class="text-xs text-slate-500">Total Debit</div>
                        <div class="text-lg font-extrabold text-emerald-700">{{ number_format((float) $totalDebit, 2) }}</div>
                    </div>
                    <div class="col-span-12 sm:col-span-3">
                        <div class="text-xs text-slate-500">Total Credit</div>
                        <div class="text-lg font-extrabold text-rose-700">{{ number_format((float) $totalCredit, 2) }}</div>
                    </div>
                </div>
            </div>

            {{-- Print table --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                    <div class="text-sm font-semibold text-slate-800">Transactions (Print)</div>
                    <div class="text-xs text-slate-500">Rows: {{ count($rows ?? []) }}</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead class="bg-slate-50 text-slate-600 uppercase">
                            <tr>
                                <th class="px-3 py-2 text-left">#</th>
                                <th class="px-3 py-2 text-left">Date</th>
                                <th class="px-3 py-2 text-left">Type</th>
                                <th class="px-3 py-2 text-left">Title / Note</th>
                                <th class="px-3 py-2 text-left">Party</th>
                                <th class="px-3 py-2 text-left">Account</th>
                                <th class="px-3 py-2 text-right">Debit</th>
                                <th class="px-3 py-2 text-right">Credit</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse($rows as $i => $tx)
                                @php
                                    $typeName = data_get($tx, 'type.name') ?? 'Type #' . $tx->transactions_type_id;
                                    [$pLabel, $pName] = $partyOf($tx);
                                    [$title, $note] = $titleOf($tx);
                                    $accountName = data_get($tx, 'account.name') ?? 'Account #' . $tx->account_id;
                                @endphp
                                <tr>
                                    <td class="px-3 py-2">{{ $i + 1 }}</td>
                                    <td class="px-3 py-2">
                                        <div class="font-semibold text-slate-800">{{ $tx->transactions_date }}</div>
                                        <div class="text-[11px] text-slate-500">
                                            #{{ $tx->id }}
                                            @if ($tx->recipt_no)
                                                • {{ $tx->recipt_no }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="font-semibold text-slate-800">{{ $typeName }}</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="font-semibold text-slate-800">{{ $title }}</div>
                                        <div class="text-[11px] text-slate-500">
                                            @if ($note)
                                                {{ $note }}
                                            @elseif($tx->student_book_number)
                                                Book: {{ $tx->student_book_number }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="font-semibold text-slate-800">{{ $pName }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $pLabel }}</div>
                                    </td>
                                    <td class="px-3 py-2">{{ $accountName }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-emerald-700">
                                        {{ number_format((float) ($tx->debit ?? 0), 2) }}
                                    </td>
                                    <td class="px-3 py-2 text-right font-semibold text-rose-700">
                                        {{ number_format((float) ($tx->credit ?? 0), 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-slate-500">No data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer note (print friendly) --}}
            <div class="text-center text-[11px] text-slate-500">
                This report is generated from Madrasha Account Management
            </div>

        </div>
    </div>

    @if ($isPdfRoute)
        <script>
            window.addEventListener('load', () => setTimeout(() => window.print(), 250));
        </script>
    @endif
</x-app-layout>