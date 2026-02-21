@php $pageTitle = 'Transactions Report'; @endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $pageTitle }}</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Filter + Export (CSV/Excel) + Print/PDF • Bangla supported
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center rounded-lg border border-slate-200 dark:border-white/10 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    Back to Dashboard
                </a>

                {{-- Print/PDF quick (opens print view) --}}
                <a href="{{ route('reports.transactions.print', request()->query()) }}"
                    class="print:hidden inline-flex items-center rounded-lg bg-slate-900 px-3 py-2 text-sm text-white hover:bg-slate-800">
                    Print / Save PDF
                </a>

            </div>
        </div>
    </x-slot>

    {{-- ✅ Print styles --}}
    <style>
        @media print {
            .print\:hidden {
                display: none !important;
            }

            body {
                background: #fff !important;
            }
        }

        @page {
            size: A4;
            margin: 12mm;
        }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- ✅ flash messages --}}
            @if (session('success'))
                <div
                    class="print:hidden bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-emerald-800 text-sm">
                    <div class="font-semibold">{{ session('success') }}</div>
                </div>
            @endif
            @if (session('error'))
                <div class="print:hidden bg-rose-50 border border-rose-200 rounded-2xl p-4 text-rose-800 text-sm">
                    <div class="font-semibold">{{ session('error') }}</div>
                </div>
            @endif

            {{-- ✅ Filters (Report Hub) --}}
            <form method="GET" action="{{ route('reports.transactions') }}"
                class="print:hidden bg-white border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm p-4">
                <div class="grid grid-cols-12 gap-3">

                    {{-- Month (priority 1) --}}
                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Month</label>
                        <input type="month" name="month" value="{{ request('month') }}"
                            class="w-full rounded-xl border-slate-200 dark:border-white/10 text-sm
                                      focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <p class="text-[11px] text-slate-400 mt-1">Month দিলে From/To ignore হবে</p>
                    </div>

                    {{-- Year (priority 2) --}}
                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Year</label>
                        <input type="number" name="year" value="{{ request('year') }}" placeholder="2026"
                            class="w-full rounded-xl border-slate-200 dark:border-white/10 text-sm
                                      focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <p class="text-[11px] text-slate-400 mt-1">Year দিলে From/To ignore হবে</p>
                    </div>

                    {{-- From / To (priority 3) --}}
                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">From</label>
                        <input type="date" name="from" value="{{ $from }}"
                            class="w-full rounded-xl border-slate-200 dark:border-white/10 text-sm
                                      focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">To</label>
                        <input type="date" name="to" value="{{ $to }}"
                            class="w-full rounded-xl border-slate-200 dark:border-white/10 text-sm
                                      focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    {{-- Account --}}
                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Account</label>
                        <select name="account_id"
                            class="w-full rounded-xl border-slate-200 dark:border-white/10 text-sm
                                       focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All</option>
                            @foreach ($accounts ?? [] as $a)
                                <option value="{{ $a->id }}" @selected((string) $a->id === (string) request('account_id'))>
                                    {{ $a->name ?? 'Account #' . $a->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Type --}}
                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Type</label>
                        <select name="type_id"
                            class="w-full rounded-xl border-slate-200 dark:border-white/10 text-sm
                                       focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All</option>
                            @foreach ($types ?? [] as $t)
                                <option value="{{ $t->id }}" @selected((string) $t->id === (string) request('type_id'))>
                                    {{ $t->name ?? 'Type #' . $t->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="col-span-12 sm:col-span-6">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full rounded-xl border-slate-200 dark:border-white/10 text-sm
                                      focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            placeholder="receipt / note / keyword / বাংলা নাম (যেমন: রফিক)">
                        <p class="text-[11px] text-slate-400 mt-1">Search Bangla/English both supported</p>
                    </div>

                    {{-- Actions --}}
                    {{-- Actions --}}
                    <div class="col-span-12 sm:col-span-6 flex items-end gap-2">
                        <button
                            class="w-full sm:w-auto rounded-xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                            Apply
                        </button>

                        <a class="w-full sm:w-auto text-center rounded-xl border border-slate-200 dark:border-white/10 bg-white text-sm px-4 py-2 hover:bg-slate-50"
                            href="{{ route('reports.transactions', []) }}">
                            Reset
                        </a>

                        <a class="w-full sm:w-auto text-center rounded-xl border border-slate-200 dark:border-white/10 bg-white text-sm px-4 py-2 hover:bg-slate-50"
                            href="{{ route('reports.transactions.csv', request()->query()) }}">
                            CSV
                        </a>

                        <a class="w-full sm:w-auto text-center rounded-xl border border-slate-200 dark:border-white/10 bg-white text-sm px-4 py-2 hover:bg-slate-50"
                            href="{{ route('reports.transactions.print', request()->query()) }}">
                            Print
                        </a>

                        <a class="w-full sm:w-auto text-center rounded-xl border border-slate-200 dark:border-white/10 bg-white text-sm px-4 py-2 hover:bg-slate-50"
                            href="{{ route('reports.transactions.pdf', request()->query()) }}">
                            PDF
                        </a>
                    </div>
                </div>
            </form>

            {{-- ✅ Totals summary --}}
            <div class="bg-white border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm p-4">
                <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-12 sm:col-span-4">
                        <div class="text-xs text-slate-500">Range</div>
                        <div class="text-sm font-semibold text-slate-800">
                            {{ $from }} → {{ $to }}
                        </div>
                        @if (request('month'))
                            <div class="text-[11px] text-slate-400 mt-1">Month: {{ request('month') }}</div>
                        @elseif(request('year'))
                            <div class="text-[11px] text-slate-400 mt-1">Year: {{ request('year') }}</div>
                        @endif
                    </div>

                    <div class="col-span-12 sm:col-span-4">
                        <div class="text-xs text-slate-500">Total Debit</div>
                        <div class="text-lg font-extrabold text-emerald-700">
                            {{ number_format((float) ($totalDebit ?? 0), 2) }}
                        </div>
                    </div>

                    <div class="col-span-12 sm:col-span-4">
                        <div class="text-xs text-slate-500">Total Credit</div>
                        <div class="text-lg font-extrabold text-rose-700">
                            {{ number_format((float) ($totalCredit ?? 0), 2) }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ Table --}}
            <div class="bg-white border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 dark:border-white/10 flex items-center justify-between">
                    <div class="text-sm font-semibold text-slate-800">Transactions</div>
                    <div class="text-xs text-slate-500">
                        Rows: {{ $transactions->total() }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">Date</th>
                                <th class="px-4 py-3 text-left">Type</th>
                                <th class="px-4 py-3 text-left">Title / Note</th>
                                <th class="px-4 py-3 text-left">Party</th>
                                <th class="px-4 py-3 text-left">Account</th>
                                <th class="px-4 py-3 text-right">Debit</th>
                                <th class="px-4 py-3 text-right">Credit</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse($transactions as $tx)
                                @php
                                    $typeName = data_get($tx, 'type.name') ?? 'Type #' . $tx->transactions_type_id;

                                    // ✅ Party (Bangla support: full_name priority)
                                    $studentName =
                                        data_get($tx, 'student.full_name') ??
                                        (data_get($tx, 'student.name') ?? data_get($tx, 'student.student_name'));

                                    $donorName =
                                        data_get($tx, 'donor.name') ??
                                        (data_get($tx, 'donor.donor_name') ?? data_get($tx, 'donor.doner_name'));

                                    $lenderName = data_get($tx, 'lender.name') ?? data_get($tx, 'lender.lender_name');

                                    $partyLabel = $studentName
                                        ? 'Student'
                                        : ($donorName
                                            ? 'Donor'
                                            : ($lenderName
                                                ? 'Lender'
                                                : '-'));
                                    $partyName = $studentName ?: ($donorName ?: ($lenderName ?: '-'));

                                    $accountName = data_get($tx, 'account.name') ?? 'Account #' . $tx->account_id;

                                    // ✅ Title/Note: c_s_1 > note
                                    $title = trim((string) ($tx->c_s_1 ?? ''));
                                    $note = trim((string) ($tx->note ?? ''));

                                    if ($title === '' && $note !== '') {
                                        $title = $note;
                                        $note = '';
                                    }
                                @endphp

                                <tr class="hover:bg-slate-50/70">
                                    {{-- Date --}}
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="font-medium text-slate-800">{{ $tx->transactions_date }}</div>
                                        <div class="text-xs text-slate-500">
                                            #{{ $tx->id }}
                                            @if ($tx->recipt_no)
                                                • {{ $tx->recipt_no }}
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Type --}}
                                    <td class="px-4 py-3">
                                        <div class="text-slate-800 font-medium">{{ $typeName }}</div>
                                        @if ($tx->income_id || $tx->expens_id)
                                            <div class="text-xs text-slate-500">
                                                @if ($tx->income_id)
                                                    IncomeHead: {{ $tx->income_id }}
                                                @endif
                                                @if ($tx->expens_id)
                                                    @if ($tx->income_id)
                                                        •
                                                    @endif ExpenseHead: {{ $tx->expens_id }}
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    {{-- ✅ Title / Note (Party style) --}}
                                    <td class="px-4 py-3">
                                        <div class="text-slate-800 font-medium">
                                            {{ $title !== '' ? $title : '-' }}
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            @if ($note !== '')
                                                {{ $note }}
                                            @elseif($tx->student_book_number)
                                                Book: {{ $tx->student_book_number }}
                                            @endif
                                        </div>
                                    </td>

                                    {{-- ✅ Party (same style) --}}
                                    <td class="px-4 py-3">
                                        <div class="text-slate-800 font-medium">{{ $partyName }}</div>
                                        <div class="text-xs text-slate-500">
                                            {{ $partyLabel }}
                                            @if ($tx->student_book_number)
                                                • Book: {{ $tx->student_book_number }}
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Account --}}
                                    <td class="px-4 py-3">{{ $accountName }}</td>

                                    {{-- Amounts --}}
                                    <td class="px-4 py-3 text-right text-emerald-700 font-semibold">
                                        {{ number_format((float) ($tx->debit ?? 0), 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-rose-700 font-semibold">
                                        {{ number_format((float) ($tx->credit ?? 0), 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-slate-500">
                                        No data
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-slate-200 dark:border-white/10">
                    {{ $transactions->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
