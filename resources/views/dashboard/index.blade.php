{{-- resources/views/dashboard/index.blade.php --}}

@php
    $pageTitle = 'Dashboard';

    // ----------------------------
    // Helpers
    // ----------------------------
    $money = fn($n) => number_format((float) $n, 2);

    // ✅ Correct meaning: Income = CREDIT, Expense = DEBIT
    $totalIn = (float) ($totalCredit ?? 0); // credit
    $totalOut = (float) ($totalDebit ?? 0); // debit
    $netValue = (float) ($net ?? 0); // credit - debit

    // Filters query string (keep clean)
    $qs = array_filter(
        [
            'from' => $from ?? null,
            'to' => $to ?? null,
            'account_id' => $accountId ?? null,
            'type_id' => $typeId ?? null,
        ],
        fn($v) => !is_null($v) && $v !== '',
    );

    // Handy links
    $linkReports = route('reports.transactions', $qs);
    $linkTC = route('transactions.center');
    $linkMonthly = route(
        'reports.monthly-statement',
        array_filter(['month' => now()->format('m'), 'year' => now()->format('Y')]),
    );
    $linkYearly = route('reports.yearly-summary', ['year' => now()->format('Y')]);

    $linkStudents = route('students.index');
    $linkBoarding = route('boarding.students.index');
    $linkSettings = route('settings.index');

    // type based report links (only if ids exist)
    $linkFeeDetails =
        isset($studentFeeTypeId) && $studentFeeTypeId
            ? route('reports.transactions', array_filter($qs + ['type_id' => $studentFeeTypeId]))
            : $linkReports;

    $linkExpenseDetails =
        isset($expenseTypeId) && $expenseTypeId
            ? route('reports.transactions', array_filter($qs + ['type_id' => $expenseTypeId]))
            : $linkReports;

    $linkIncomeDetails =
        isset($incomeTypeId) && $incomeTypeId
            ? route('reports.transactions', array_filter($qs + ['type_id' => $incomeTypeId]))
            : $linkReports;

    $linkDonationDetails =
        isset($donationTypeId) && $donationTypeId
            ? route('reports.transactions', array_filter($qs + ['type_id' => $donationTypeId]))
            : $linkReports;

    $linkLoanTakenDetails =
        isset($loanTakenTypeId) && $loanTakenTypeId
            ? route('reports.transactions', array_filter($qs + ['type_id' => $loanTakenTypeId]))
            : $linkReports;

    $linkLoanRepayDetails =
        isset($loanRepayTypeId) && $loanRepayTypeId
            ? route('reports.transactions', array_filter($qs + ['type_id' => $loanRepayTypeId]))
            : $linkReports;

    // ----------------------------
    // UI Tokens (Global Standard)
    // ----------------------------
    $muted = 'text-slate-500 dark:text-slate-400';
    $title = 'text-slate-900 dark:text-slate-100';

    // ✅ User preference: consistent input border/focus
    $inputBase = 'w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900/40
                  text-sm text-slate-900 dark:text-slate-100
                  focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500';

    $btnPrimary = 'inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2 text-sm font-semibold text-white
                   hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500';

    $btnSoft = 'inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 dark:border-white/10
                bg-white dark:bg-slate-900/40 px-3.5 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200
                hover:bg-slate-50 dark:hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-emerald-500';

    $btnWarn = 'inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-3.5 py-2 text-sm font-semibold text-white
                hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-emerald-500';

    $cardBase = 'group relative overflow-hidden rounded-3xl border border-slate-200 dark:border-white/10
                 bg-white dark:bg-slate-900/40 shadow-sm
                 hover:shadow-md hover:-translate-y-0.5 transition
                 focus:outline-none focus:ring-2 focus:ring-emerald-500';

    $panelBase =
        'overflow-hidden rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900/40 shadow-sm';
    $panelHeader = 'px-4 py-3 border-b border-slate-200 dark:border-white/10 bg-slate-50/60 dark:bg-white/5';

    $tableHead = 'bg-slate-50 dark:bg-white/5 text-slate-600 dark:text-slate-300 text-xs uppercase';
    $tableRow = 'hover:bg-slate-50/70 dark:hover:bg-white/5';
    $tableCell = 'px-4 py-3';

    $updatedText = now()->format('d M Y • h:i A');

    // Preset ranges
    $today = now()->toDateString();
    $monthStart = now()->startOfMonth()->toDateString();
    $monthEnd = now()->endOfMonth()->toDateString();
    $yearStart = now()->startOfYear()->toDateString();
    $yearEnd = now()->endOfYear()->toDateString();

    $presets = [
        ['label' => 'Today', 'qs' => ['from' => $today, 'to' => $today]],
        ['label' => 'This Month', 'qs' => ['from' => $monthStart, 'to' => $monthEnd]],
        ['label' => 'This Year', 'qs' => ['from' => $yearStart, 'to' => $yearEnd]],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        {{-- ✅ Compact Premium Header (less empty space) --}}
        <div class="flex flex-col gap-3">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h2 class="font-semibold text-xl leading-tight text-slate-800 dark:text-slate-100">
                            {{ $pageTitle }}
                        </h2>
                        <span
                            class="hidden sm:inline-flex items-center rounded-full border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-2.5 py-1 text-[11px] font-semibold {{ $muted }}">
                            Updated: {{ $updatedText }}
                        </span>
                    </div>

                    <p class="text-xs {{ $muted }} mt-1">
                        One glance overview • click any card for details • filters apply across all widgets
                    </p>

                    {{-- ✅ Presets row (compact) --}}
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        @foreach ($presets as $p)
                            <a href="{{ route('dashboard', $p['qs']) }}"
                                class="inline-flex items-center rounded-full border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900/40
                                       px-3 py-1.5 text-[12px] font-semibold text-slate-700 dark:text-slate-200
                                       hover:bg-slate-50 dark:hover:bg-white/5">
                                {{ $p['label'] }}
                            </a>
                        @endforeach

                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center rounded-full px-3 py-1.5 text-[12px] font-semibold text-emerald-700 hover:underline">
                            Reset →
                        </a>

                        <span
                            class="sm:hidden inline-flex items-center rounded-full border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-2.5 py-1 text-[11px] font-semibold {{ $muted }}">
                            Updated: {{ $updatedText }}
                        </span>
                    </div>
                </div>

                {{-- ✅ Actions (same links, compact) --}}
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ $linkTC }}" class="{{ $btnPrimary }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5" />
                        </svg>
                        Transaction Center
                    </a>

                    <a href="{{ $linkMonthly }}" class="{{ $btnSoft }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3M4 11h16M6 21h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Monthly Statement
                    </a>

                    <a href="{{ $linkYearly }}" class="{{ $btnSoft }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-6m4 6V7m4 10v-4M4 21h16" />
                        </svg>
                        Yearly Summary
                    </a>

                    <a href="{{ $linkReports }}" class="{{ $btnSoft }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17h6m-6-4h6m-6-4h6M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Reports
                    </a>

                    <a href="{{ $linkSettings }}" class="{{ $btnWarn }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15.5A3.5 3.5 0 1112 8.5a3.5 3.5 0 010 7z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.4 15a1.8 1.8 0 00.36 1.98l.06.06-1.7 2.95-.08-.02a1.8 1.8 0 00-2.06.82l-.04.07H8.06l-.04-.07a1.8 1.8 0 00-2.06-.82l-.08.02-1.7-2.95.06-.06A1.8 1.8 0 004.6 15l-.02-.08V9.08l.02-.08A1.8 1.8 0 004.24 7.02l-.06-.06 1.7-2.95.08.02a1.8 1.8 0 002.06-.82l.04-.07h7.88l.04.07a1.8 1.8 0 002.06.82l.08-.02 1.7 2.95-.06.06A1.8 1.8 0 0019.4 9l.02.08v5.84z" />
                        </svg>
                        Settings
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-3">

            {{-- ✅ Premium Compact Hero + Better Totals --}}
            <div
                class="relative overflow-hidden rounded-3xl border border-slate-200 dark:border-white/10 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 shadow-sm">
                <div class="absolute -top-24 -right-24 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute -bottom-28 -left-28 h-72 w-72 rounded-full bg-emerald-500/10 blur-3xl"></div>

                <div class="relative p-5 sm:p-6">
                    <div class="grid grid-cols-12 gap-4 items-stretch">
                        {{-- Left: Range --}}
                        <div class="col-span-12 lg:col-span-7 text-white min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm/6 text-white">Selected range</span>
                                <span
                                    class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-2.5 py-1 text-[11px] font-semibold">
                                    Updated: {{ $updatedText }}
                                </span>
                            </div>

                            <div class="mt-2 text-2xl sm:text-3xl font-semibold tracking-tight truncate">
                                {{ $from ?? '—' }} → {{ $to ?? '—' }}
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs opacity-85">
                                <span class="rounded-full border border-white/15 bg-white/10 px-2.5 py-1">
                                    Transactions: {{ number_format((int) ($transactionsCount ?? 0)) }}
                                </span>

                                @if (!empty($accountId))
                                    <span class="rounded-full border border-white/15 bg-white/10 px-2.5 py-1">Account
                                        filtered</span>
                                @endif

                                @if (!empty($typeId))
                                    <span class="rounded-full border border-white/15 bg-white/10 px-2.5 py-1">Type
                                        filtered</span>
                                @endif

                                <a href="{{ route('dashboard') }}"
                                    class="underline decoration-white/30 hover:decoration-white">
                                    Reset
                                </a>
                            </div>

                            {{-- ✅ In vs Out stacked bar --}}
                            @php
                                $totalFlow = max(1, (float) ($totalIn + $totalOut));
                                $inPct = ($totalIn / $totalFlow) * 100;
                                $outPct = ($totalOut / $totalFlow) * 100;
                            @endphp

                            <div class="mt-4">
                                <div class="flex items-center justify-between text-[11px] opacity-85 mb-1">
                                    <span>Flow (Credit vs Debit)</span>
                                    <span class="font-semibold">{{ (int) $inPct }}% / {{ (int) $outPct }}%</span>
                                </div>

                                <div class="h-2.5 rounded-full bg-white/10 overflow-hidden border border-white/10">
                                    <div class="h-2.5 bg-emerald-500/70" style="width: {{ $inPct }}%"></div>
                                </div>
                                <div class="mt-2 h-2.5 rounded-full bg-white/10 overflow-hidden border border-white/10">
                                    <div class="h-2.5 bg-rose-500/70" style="width: {{ $outPct }}%"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Right: Totals (bigger, glass, premium) --}}
                        <div class="col-span-12 lg:col-span-5">
                            <div class="grid grid-cols-3 lg:grid-cols-1 gap-3 h-full">
                                <div
                                    class="rounded-2xl border border-white/10 bg-white/10 backdrop-blur p-4 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="text-[11px] text-white">Total In (Credit)</div>
                                        <div class="mt-1 text-xl font-semibold text-emerald-100 truncate">
                                            {{ $money($totalIn) }}
                                        </div>
                                    </div>
                                    <div
                                        class="h-10 w-10 rounded-2xl bg-emerald-500/15 border border-emerald-400/20 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-emerald-100" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 17l10-10M7 7h10v10" />
                                        </svg>
                                    </div>
                                </div>

                                <div
                                    class="rounded-2xl border border-white/10 bg-white/10 backdrop-blur p-4 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="text-[11px] text-white">Total Out (Debit)</div>
                                        <div class="mt-1 text-xl font-semibold text-rose-100 truncate">
                                            {{ $money($totalOut) }}
                                        </div>
                                    </div>
                                    <div
                                        class="h-10 w-10 rounded-2xl bg-rose-500/15 border border-rose-400/20 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-rose-100" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 7L7 17M7 7h10v10" />
                                        </svg>
                                    </div>
                                </div>

                                <div
                                    class="rounded-2xl border border-white/10 bg-white/10 backdrop-blur p-4 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="text-[11px] text-white">Net (Credit − Debit)</div>
                                        <div
                                            class="mt-1 text-xl font-semibold truncate {{ $netValue >= 0 ? 'text-emerald-100' : 'text-rose-100' }}">
                                            {{ $money($netValue) }}
                                        </div>
                                    </div>
                                    <div
                                        class="h-10 w-10 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 19h16M7 15l3-3 3 3 4-6" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ✅ Compact footer row --}}
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-2 text-xs text-white/70">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-1">Income =
                                Credit</span>
                            <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-1">Expense =
                                Debit</span>
                            <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-1">Loan Taken =
                                Credit • Loan Repayment = Debit</span>
                        </div>

                        <a href="{{ $linkReports }}" class="font-semibold text-emerald-200 hover:underline">
                            View details →
                        </a>
                    </div>
                </div>
            </div>

            {{-- ✅ Filters --}}
            <form method="GET" action="{{ route('dashboard') }}" class="{{ $panelBase }} p-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="text-sm font-semibold {{ $title }}">Filters</div>
                        <div class="text-xs {{ $muted }}">Change date, account or type and apply</div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('dashboard') }}" class="{{ $btnSoft }}">Reset</a>
                        <button type="submit" class="{{ $btnPrimary }}">Apply Filters</button>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-12 gap-3">
                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium {{ $muted }} mb-1">From</label>
                        <input type="date" name="from" value="{{ $from }}"
                            class="{{ $inputBase }}">
                    </div>

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium {{ $muted }} mb-1">To</label>
                        <input type="date" name="to" value="{{ $to }}"
                            class="{{ $inputBase }}">
                    </div>

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium {{ $muted }} mb-1">Account</label>
                        <select name="account_id" class="{{ $inputBase }}">
                            <option value="">All accounts</option>
                            @foreach ($accounts ?? [] as $acc)
                                <option value="{{ $acc->id }}" @selected((string) $acc->id === (string) $accountId)>
                                    {{ $acc->name ?? 'Account #' . $acc->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium {{ $muted }} mb-1">Type</label>
                        <select name="type_id" class="{{ $inputBase }}">
                            <option value="">All types</option>
                            @foreach ($types ?? [] as $t)
                                <option value="{{ $t->id }}" @selected((string) $t->id === (string) $typeId)>
                                    {{ $t->name ?? 'Type #' . $t->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            {{-- ✅ Quick Actions --}}
            @php
                $quick = [
                    [
                        'href' => $linkTC,
                        'title' => 'Transaction Center',
                        'sub' => 'Create fee, donation, income, expense, loan',
                        'bar' => 'bg-emerald-500/80',
                    ],
                    [
                        'href' => $linkReports,
                        'title' => 'Transactions Report',
                        'sub' => 'Filter, CSV export, print/PDF view',
                        'bar' => 'bg-sky-500/80',
                    ],
                    [
                        'href' => $linkMonthly,
                        'title' => 'Monthly Statement',
                        'sub' => 'Paper format: income vs expense',
                        'bar' => 'bg-violet-500/80',
                    ],
                    [
                        'href' => $linkYearly,
                        'title' => 'Yearly Summary',
                        'sub' => 'Month-wise totals & yearly totals',
                        'bar' => 'bg-amber-500/80',
                    ],
                    [
                        'href' => $linkStudents,
                        'title' => 'Students',
                        'sub' => 'Admission list, profile, filters',
                        'bar' => 'bg-indigo-500/80',
                    ],
                    [
                        'href' => $linkBoarding,
                        'title' => 'Boarding Students',
                        'sub' => 'is_boarding = true list & toggle',
                        'bar' => 'bg-rose-500/80',
                    ],
                ];
            @endphp

            <div>
                <div class="flex items-end justify-between gap-3 mb-2">
                    <div>
                        <div class="text-sm font-semibold {{ $title }}">Quick Actions</div>
                        <div class="text-xs {{ $muted }}">Jump directly to important pages</div>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-3">
                    @foreach ($quick as $q)
                        <a href="{{ $q['href'] }}"
                            class="{{ $cardBase }} col-span-12 sm:col-span-6 lg:col-span-4">
                            <div class="absolute inset-x-0 top-0 h-1 {{ $q['bar'] }}"></div>

                            <div class="p-4 pt-5">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="h-10 w-10 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-slate-700 dark:text-slate-200" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7h8M13 12h8M13 17h8M3 7h6v6H3V7zM3 17h6v-4H3v4z" />
                                        </svg>
                                    </div>

                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold {{ $title }}">{{ $q['title'] }}
                                        </div>
                                        <div class="mt-1 text-xs {{ $muted }}">{{ $q['sub'] }}</div>
                                        <div
                                            class="mt-3 text-[11px] font-semibold text-emerald-700 group-hover:underline">
                                            Click for more details →
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ✅ KPI Cards --}}
            @php
                $totalBalance = collect($accountBalances ?? [])->sum(fn($r) => (float) ($r->balance ?? 0));

                $kpis = [
                    [
                        'href' => $linkStudents,
                        'bar' => 'bg-indigo-500/80',
                        'label' => 'Students',
                        'value' => number_format((int) ($totalStudents ?? 0)),
                        'note' =>
                            'Paid: ' .
                            number_format((int) ($studentsPaidCount ?? 0)) .
                            ' • Due: ' .
                            number_format((int) ($studentsUnpaidApprox ?? 0)),
                    ],
                    [
                        'href' => $linkFeeDetails,
                        'bar' => 'bg-emerald-500/80',
                        'label' => 'Fees Collected (Credit)',
                        'value' => $money($feesCollected ?? 0),
                        'note' => 'Student fee credit sum (within filter)',
                    ],
                    [
                        'href' => $linkExpenseDetails,
                        'bar' => 'bg-rose-500/80',
                        'label' => 'Total Expense (Debit)',
                        'value' => $money($expenseTotal ?? 0),
                        'note' => 'Expense debit sum (within filter)',
                    ],
                    [
                        'href' => $linkIncomeDetails,
                        'bar' => 'bg-sky-500/80',
                        'label' => 'Total Income (Credit)',
                        'value' => $money($incomeTotal ?? 0),
                        'note' => 'Income credit sum (within filter)',
                    ],
                    [
                        'href' => $linkDonationDetails,
                        'bar' => 'bg-amber-500/80',
                        'label' => 'Donation (Credit)',
                        'value' => $money($donationTotal ?? 0),
                        'note' => 'Donation credit sum (within filter)',
                    ],
                    [
                        'href' => $linkReports,
                        'bar' => 'bg-slate-500/80',
                        'label' => 'Loan Outstanding',
                        'value' => $money($loanOutstanding ?? 0),
                        'note' =>
                            'Taken ' . $money($loanTakenTotal ?? 0) . ' − Repay ' . $money($loanRepaymentTotal ?? 0),
                    ],
                    [
                        'href' => route('account.index'),
                        'bar' => 'bg-violet-500/80',
                        'label' => 'Total Bank Balance',
                        'value' => $money($totalBalance),
                        'note' => 'Balance = Credit − Debit (within filter)',
                    ],
                    [
                        'href' => $linkReports,
                        'bar' => $netValue >= 0 ? 'bg-emerald-500/80' : 'bg-rose-500/80',
                        'label' => 'Net (Credit − Debit)',
                        'value' => $money($netValue),
                        'note' => 'In vs Out progress on hero',
                    ],
                    [
                        'href' => $linkReports,
                        'bar' => 'bg-teal-500/80',
                        'label' => 'Transactions (Range)',
                        'value' => number_format((int) ($transactionsCount ?? 0)),
                        'note' => 'All types included (based on filter)',
                    ],
                ];
            @endphp

            <div>
                <div class="flex items-end justify-between gap-3 mb-2">
                    <div>
                        <div class="text-sm font-semibold {{ $title }}">Snapshot</div>
                        <div class="text-xs {{ $muted }}">Key numbers based on selected filter</div>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-3">
                    @foreach ($kpis as $k)
                        @php
                            $valueClass = 'text-emerald-700';
                            if ($k['label'] === 'Total Expense (Debit)') {
                                $valueClass = 'text-rose-700';
                            }
                            if ($k['label'] === 'Net (Credit − Debit)') {
                                $valueClass = $netValue >= 0 ? 'text-emerald-700' : 'text-rose-700';
                            }
                            if (in_array($k['label'], ['Loan Outstanding', 'Students', 'Transactions (Range)'])) {
                                $valueClass = $title;
                            }
                        @endphp

                        <a href="{{ $k['href'] }}"
                            class="{{ $cardBase }} col-span-12 sm:col-span-6 lg:col-span-4">
                            <div class="absolute inset-x-0 top-0 h-1 {{ $k['bar'] }}"></div>

                            <div class="p-4 pt-5">
                                <div class="text-xs {{ $muted }}">{{ $k['label'] }}</div>
                                <div class="mt-1 text-2xl font-semibold {{ $valueClass }}">
                                    {{ $k['value'] }}
                                </div>
                                <div class="mt-2 text-[11px] {{ $muted }}">{{ $k['note'] }}</div>

                                <div class="mt-3 text-[11px] font-semibold text-emerald-700 group-hover:underline">
                                    Click for more details →
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ✅ Extra Insights --}}
            <div class="grid grid-cols-12 gap-3">

                {{-- Fees Due --}}
                <div class="col-span-12 lg:col-span-4">
                    <div class="{{ $panelBase }}">
                        <div class="{{ $panelHeader }}">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold {{ $title }}">Fees Due</div>
                                    <div class="text-xs {{ $muted }}">Selected range এ যারা fee দেয়নি</div>
                                </div>

                                <div class="text-right">
                                    <div class="text-[11px] {{ $muted }}">Due Students</div>
                                    <div
                                        class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                        {{ number_format((int) ($studentsDueCount ?? 0)) }}
                                    </div>
                                </div>
                            </div>

                            @php
                                $paid = (int) ($studentsPaidCount ?? 0);
                                $total = max(1, (int) ($totalStudents ?? 0));
                                $paidPct = min(100, ($paid / $total) * 100);
                            @endphp

                            <div class="mt-3">
                                <div class="flex items-center justify-between text-[11px] {{ $muted }} mb-1">
                                    <span>Paid progress</span>
                                    <span
                                        class="font-semibold text-slate-700 dark:text-slate-200">{{ (int) $paidPct }}%</span>
                                </div>
                                <div
                                    class="h-2 rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden border border-slate-200 dark:border-white/10">
                                    <div class="h-2 bg-emerald-500/70" style="width: {{ $paidPct }}%"></div>
                                </div>
                            </div>

                            <a href="{{ $linkStudents }}"
                                class="mt-3 inline-block text-[11px] font-semibold text-emerald-700 hover:underline">
                                Click for more details →
                            </a>
                        </div>

                        <div class="p-4">
                            <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 mb-2">Top due students
                            </div>

                            <div class="space-y-2">
                                @forelse(($dueStudents ?? []) as $s)
                                    @php
                                        $nm =
                                            data_get($s, 'full_name') ??
                                            (data_get($s, 'student.full_name') ??
                                                ($s->student_name ?? 'Student #' . $s->id));
                                        $initial = strtoupper(mb_substr(trim($nm), 0, 1));
                                    @endphp

                                    <div
                                        class="flex items-center justify-between rounded-2xl border border-slate-200 dark:border-white/10 px-3 py-2 hover:bg-slate-50 dark:hover:bg-white/5">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div
                                                class="h-9 w-9 shrink-0 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-semibold">
                                                {{ $initial }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-sm font-medium {{ $title }} truncate">
                                                    {{ $nm }}</div>
                                                <div class="text-[11px] {{ $muted }}">ID: {{ $s->id }}
                                                </div>
                                            </div>
                                        </div>

                                        <a href="{{ route('students.show', $s->id) }}"
                                            class="text-xs font-semibold text-slate-700 dark:text-slate-200 hover:underline">
                                            View
                                        </a>
                                    </div>
                                @empty
                                    <div class="text-sm {{ $muted }}">No due students 🎉</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fees Paid --}}
                <div class="col-span-12 lg:col-span-4">
                    <div class="{{ $panelBase }}">
                        <div class="{{ $panelHeader }}">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold {{ $title }}">Fees Paid</div>
                                    <div class="text-xs {{ $muted }}">Selected range এ যারা fee দিয়েছে</div>
                                </div>

                                <div class="text-right">
                                    <div class="text-[11px] {{ $muted }}">Paid Students</div>
                                    <div
                                        class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800
                                                dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-200">
                                        {{ number_format((int) ($studentsPaidCountExact ?? ($studentsPaidCount ?? 0))) }}
                                    </div>
                                </div>
                            </div>

                            @php
                                $paidExact = (int) ($studentsPaidCountExact ?? ($studentsPaidCount ?? 0));
                                $totalStu = max(1, (int) ($totalStudents ?? 0));
                                $paidPct2 = min(100, ($paidExact / $totalStu) * 100);
                            @endphp

                            <div class="mt-3">
                                <div class="flex items-center justify-between text-[11px] {{ $muted }} mb-1">
                                    <span>Paid progress</span>
                                    <span
                                        class="font-semibold text-slate-700 dark:text-slate-200">{{ (int) $paidPct2 }}%</span>
                                </div>
                                <div
                                    class="h-2 rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden border border-slate-200 dark:border-white/10">
                                    <div class="h-2 bg-emerald-500/70" style="width: {{ $paidPct2 }}%"></div>
                                </div>
                            </div>

                            <a href="{{ $linkFeeDetails }}"
                                class="mt-3 inline-block text-[11px] font-semibold text-emerald-700 hover:underline">
                                Click for more details →
                            </a>
                        </div>

                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-xs font-semibold text-slate-800 dark:text-slate-200">Top paid students
                                </div>
                                <div class="text-xs {{ $muted }}">
                                    Collected: <span
                                        class="font-semibold text-emerald-700">{{ $money($feesCollected ?? 0) }}</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                @forelse(($paidStudents ?? []) as $p)
                                    @php
                                        $nm =
                                            data_get($p, 'full_name') ??
                                            (data_get($p, 'student.full_name') ??
                                                ($p->student_name ?? 'Student #' . $p->id));
                                        $initial = strtoupper(mb_substr(trim($nm), 0, 1));
                                        $amt = (float) ($p->paid_total ?? 0);
                                        $dt = $p->last_paid_date ?? null;
                                    @endphp

                                    <div
                                        class="flex items-center justify-between rounded-2xl border border-slate-200 dark:border-white/10 px-3 py-2 hover:bg-slate-50 dark:hover:bg-white/5">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div
                                                class="h-9 w-9 shrink-0 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-semibold">
                                                {{ $initial }}
                                            </div>

                                            <div class="min-w-0">
                                                <div class="text-sm font-medium {{ $title }} truncate">
                                                    {{ $nm }}</div>
                                                <div class="text-[11px] {{ $muted }}">
                                                    ID: {{ $p->id }} • Paid: <span
                                                        class="font-semibold text-emerald-700">{{ $money($amt) }}</span>
                                                    @if ($dt)
                                                        • {{ $dt }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <a href="{{ route('students.show', $p->id) }}"
                                            class="text-xs font-semibold text-slate-700 dark:text-slate-200 hover:underline">
                                            View
                                        </a>
                                    </div>
                                @empty
                                    <div class="text-sm {{ $muted }}">No paid students found in this range.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Loan Overview --}}
                <div class="col-span-12 lg:col-span-4">
                    <div class="{{ $panelBase }}">
                        <div class="{{ $panelHeader }}">
                            <div class="text-sm font-semibold {{ $title }}">Loan Overview</div>
                            <div class="text-xs {{ $muted }}">Taken (Credit) vs Paid (Debit)</div>

                            @php
                                $loanTaken = (float) ($loanTakenTotal ?? 0);
                                $loanPaid = (float) ($loanRepaymentTotal ?? 0);
                                $loanOut = (float) ($loanOutstanding ?? 0);
                                $loanPct = (float) ($loanPaidPct ?? 0);
                            @endphp

                            <div class="mt-4 grid grid-cols-3 gap-2">
                                <a href="{{ $linkLoanTakenDetails }}"
                                    class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900/40 p-3 hover:bg-slate-50 dark:hover:bg-white/5">
                                    <div class="text-[11px] {{ $muted }}">Taken</div>
                                    <div class="text-sm font-semibold text-emerald-700">{{ $money($loanTaken) }}
                                    </div>
                                    <div class="text-[11px] text-emerald-700 mt-2 font-semibold">details →</div>
                                </a>

                                <a href="{{ $linkLoanRepayDetails }}"
                                    class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900/40 p-3 hover:bg-slate-50 dark:hover:bg-white/5">
                                    <div class="text-[11px] {{ $muted }}">Paid</div>
                                    <div class="text-sm font-semibold text-rose-700">{{ $money($loanPaid) }}</div>
                                    <div class="text-[11px] text-emerald-700 mt-2 font-semibold">details →</div>
                                </a>

                                <div
                                    class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900/40 p-3">
                                    <div class="text-[11px] {{ $muted }}">Outstanding</div>
                                    <div class="text-sm font-semibold {{ $title }}">{{ $money($loanOut) }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="flex items-center justify-between text-[11px] {{ $muted }} mb-1">
                                    <span>Paid progress</span>
                                    <span
                                        class="font-semibold text-slate-700 dark:text-slate-200">{{ (int) $loanPct }}%</span>
                                </div>
                                <div
                                    class="h-2 rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden border border-slate-200 dark:border-white/10">
                                    <div class="h-2 bg-emerald-500/70" style="width: {{ $loanPct }}%"></div>
                                </div>
                            </div>

                            <a href="{{ $linkReports }}"
                                class="mt-3 inline-block text-[11px] font-semibold text-emerald-700 hover:underline">
                                Click for more details →
                            </a>
                        </div>

                        <div class="p-4">
                            <div
                                class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900/40 p-3">
                                <div class="text-xs {{ $muted }}">Rule</div>
                                <div class="text-sm text-slate-800 dark:text-slate-200">
                                    Loan Taken = credit • Loan Repayment = debit
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ✅ Expense Breakdown (Row-wise + Full width, no empty gap) --}}
                <div class="col-span-12">
                    <div class="{{ $panelBase }}">
                        <div class="{{ $panelHeader }} flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold {{ $title }}">Expense Breakdown</div>
                                <div class="text-xs {{ $muted }}">Category-wise debit total (row wise)</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[11px] {{ $muted }}">Total</div>
                                <div class="text-sm font-semibold text-rose-700">{{ $money($expenseTotal ?? 0) }}
                                </div>
                            </div>
                        </div>

                        <div class="p-4">
                            @php $expTotal = max(1, (float)($expenseTotal ?? 0)); @endphp

                            <div class="rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
                                {{-- header row --}}
                                <div
                                    class="grid grid-cols-12 gap-3 px-3 py-2 bg-slate-50/70 dark:bg-white/5 text-[11px] font-semibold text-slate-600 dark:text-slate-300">
                                    <div class="col-span-7 sm:col-span-6">Category</div>
                                    <div class="col-span-5 sm:col-span-2 text-right">Amount</div>
                                    <div class="hidden sm:block sm:col-span-4">Share</div>
                                </div>

                                <div class="divide-y divide-slate-100 dark:divide-white/10">
                                    @forelse(($expenseByCategory ?? []) as $row)
                                        @php
                                            $cat = $row->category_name ?? 'Uncategorized';
                                            $amt = (float) ($row->total ?? 0);
                                            $pct = min(100, ($amt / $expTotal) * 100);
                                        @endphp

                                        {{-- whole row clickable --}}
                                        <a href="{{ $linkExpenseDetails }}"
                                            class="block px-3 py-3 hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                            <div class="grid grid-cols-12 gap-3 items-center">
                                                <div class="col-span-7 sm:col-span-6 min-w-0">
                                                    <div class="text-sm font-semibold {{ $title }} truncate">
                                                        {{ $cat }}</div>
                                                    <div class="text-[11px] {{ $muted }}">
                                                        {{ (int) $pct }}% of total</div>
                                                </div>

                                                <div class="col-span-5 sm:col-span-2 text-right">
                                                    <div class="text-sm font-semibold text-rose-700">
                                                        {{ $money($amt) }}</div>
                                                    <div class="text-[11px] text-emerald-700 font-semibold">details →
                                                    </div>
                                                </div>

                                                <div class="hidden sm:block sm:col-span-4">
                                                    <div
                                                        class="h-2 rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden border border-slate-200 dark:border-white/10">
                                                        <div class="h-2 bg-rose-500/70"
                                                            style="width: {{ $pct }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="p-4">
                                            <div class="text-sm font-semibold {{ $title }}">Breakdown পাওয়া
                                                যায়নি</div>
                                            <div class="text-xs {{ $muted }} mt-1">
                                                `transactions.catagory_id` অথবা `transactions.expens_id` + related
                                                tables না থাকলে breakdown হবে না।
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between">
                                <a href="{{ $linkExpenseDetails }}"
                                    class="text-[11px] font-semibold text-emerald-700 hover:underline">
                                    View all expense details →
                                </a>
                                <span class="text-[11px] {{ $muted }}">Tip: click any row for details</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ✅ Account balances + Recent --}}
            <div class="grid grid-cols-12 gap-3">

                {{-- Account Balances --}}
                <div class="col-span-12 lg:col-span-5 {{ $panelBase }}">
                    <div class="{{ $panelHeader }} flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold {{ $title }}">Bank / Account Balance</div>
                            <div class="text-xs {{ $muted }}">Balance = Credit − Debit (within selected
                                filter)</div>
                        </div>
                        <a href="{{ route('account.index') }}"
                            class="text-xs font-semibold text-emerald-700 hover:underline">
                            Click for more details →
                        </a>
                    </div>

                    <div class="p-4 space-y-3">
                        @forelse($accountBalances ?? [] as $row)
                            @php
                                $bal = (float) ($row->balance ?? 0);
                                $pct = min(100, (abs($bal) / (float) ($maxAbsBalance ?? 1)) * 100);
                            @endphp

                            <div
                                class="rounded-2xl border border-slate-200 dark:border-white/10 p-3 hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <div class="font-semibold {{ $title }} truncate">
                                            {{ $row->account_name }}</div>
                                        <div class="text-[11px] {{ $muted }}">
                                            In (Credit): {{ $money($row->credit_sum ?? 0) }} • Out (Debit):
                                            {{ $money($row->debit_sum ?? 0) }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div
                                            class="text-sm font-bold {{ $bal >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                            {{ $money($bal) }}
                                        </div>
                                        <div class="text-[11px] {{ $muted }}">Balance</div>
                                    </div>
                                </div>

                                <div
                                    class="mt-3 h-2 rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden border border-slate-200 dark:border-white/10">
                                    <div class="h-2 {{ $bal >= 0 ? 'bg-emerald-500/70' : 'bg-rose-500/70' }}"
                                        style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm {{ $muted }}">No account transactions found in this range.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Transactions --}}
                <div class="col-span-12 lg:col-span-7 {{ $panelBase }}">
                    <div class="{{ $panelHeader }} flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold {{ $title }}">Recent Transactions</div>
                            <div class="text-xs {{ $muted }}">Latest 10 (within filter)</div>
                        </div>
                        <a href="{{ $linkTC }}"
                            class="text-xs font-semibold text-emerald-700 hover:underline">
                            Click for more details →
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="{{ $tableHead }}">
                                <tr>
                                    <th class="{{ $tableCell }} text-left">Date</th>
                                    <th class="{{ $tableCell }} text-left">Type</th>
                                    <th class="{{ $tableCell }} text-left">Party</th>
                                    <th class="{{ $tableCell }} text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                                @forelse($recent ?? [] as $tx)
                                    @php
                                        $typeName =
                                            data_get($tx, 'type.name') ?? 'Type #' . ($tx->transactions_type_id ?? '-');

                                        $studentName =
                                            data_get($tx, 'student.full_name') ??
                                            (data_get($tx, 'student.name') ?? data_get($tx, 'student.student_name'));

                                        $donorName =
                                            data_get($tx, 'donor.name') ??
                                            (data_get($tx, 'donor.donor_name') ?? data_get($tx, 'donor.doner_name'));

                                        $lenderName =
                                            data_get($tx, 'lender.name') ?? data_get($tx, 'lender.lender_name');

                                        $party = $studentName
                                            ? 'Student: ' . $studentName
                                            : ($donorName
                                                ? 'Donor: ' . $donorName
                                                : ($lenderName
                                                    ? 'Lender: ' . $lenderName
                                                    : '-'));

                                        $d = (float) ($tx->debit ?? 0);
                                        $c = (float) ($tx->credit ?? 0);
                                        $amount = $c > 0 ? $c : $d;
                                        $isCredit = $c > 0;
                                    @endphp

                                    <tr class="{{ $tableRow }}">
                                        <td class="{{ $tableCell }} whitespace-nowrap">
                                            <div class="font-medium {{ $title }}">
                                                {{ $tx->transactions_date ?? '-' }}</div>
                                            <div class="text-xs {{ $muted }}">#{{ $tx->id }}</div>
                                        </td>

                                        <td class="{{ $tableCell }}">
                                            <span
                                                class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900/40 px-2 py-1 text-xs">
                                                {{ $typeName }}
                                            </span>
                                        </td>

                                        <td class="{{ $tableCell }} text-slate-700 dark:text-slate-200">
                                            {{ $party }}
                                        </td>

                                        <td
                                            class="{{ $tableCell }} text-right font-semibold {{ $isCredit ? 'text-emerald-700' : 'text-rose-700' }}">
                                            {{ $money($amount) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="{{ $tableCell }} py-10 text-center {{ $muted }}">
                                            No transactions found in this range.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- ✅ Type Summary + Daily --}}
            <div class="grid grid-cols-12 gap-3">

                {{-- Type Summary --}}
                <div class="col-span-12 lg:col-span-5 {{ $panelBase }}">
                    <div class="{{ $panelHeader }}">
                        <div class="text-sm font-semibold {{ $title }}">Type Summary</div>
                        <div class="text-xs {{ $muted }}">Debit (Expense) / Credit (Income)</div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="{{ $tableHead }}">
                                <tr>
                                    <th class="{{ $tableCell }} text-left">Type</th>
                                    <th class="{{ $tableCell }} text-right">Debit</th>
                                    <th class="{{ $tableCell }} text-right">Credit</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                                @forelse($typeBreakdown ?? [] as $row)
                                    <tr class="{{ $tableRow }}">
                                        <td class="{{ $tableCell }}">
                                            <div class="font-medium {{ $title }}">{{ $row->type_name }}
                                            </div>
                                            <div class="text-xs {{ $muted }}">{{ $row->count }} tx</div>
                                        </td>
                                        <td class="{{ $tableCell }} text-right text-rose-700 font-semibold">
                                            {{ $money($row->debit_sum) }}</td>
                                        <td class="{{ $tableCell }} text-right text-emerald-700 font-semibold">
                                            {{ $money($row->credit_sum) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3"
                                            class="{{ $tableCell }} py-10 text-center {{ $muted }}">No
                                            data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Daily Summary --}}
                <div class="col-span-12 lg:col-span-7 {{ $panelBase }}">
                    <div class="{{ $panelHeader }} flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold {{ $title }}">Daily Summary</div>
                            <div class="text-xs {{ $muted }}">Net per day (Credit − Debit)</div>
                        </div>
                        <a href="{{ $linkReports }}"
                            class="text-xs font-semibold text-emerald-700 hover:underline">
                            Click for more details →
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="{{ $tableHead }}">
                                <tr>
                                    <th class="{{ $tableCell }} text-left">Date</th>
                                    <th class="{{ $tableCell }} text-right">Debit</th>
                                    <th class="{{ $tableCell }} text-right">Credit</th>
                                    <th class="{{ $tableCell }} text-right">Net</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                                @forelse($daily ?? [] as $d)
                                    <tr class="{{ $tableRow }}">
                                        <td class="{{ $tableCell }} text-slate-700 dark:text-slate-200">
                                            {{ $d->d }}</td>
                                        <td class="{{ $tableCell }} text-right text-rose-700 font-semibold">
                                            {{ $money($d->debit_sum) }}</td>
                                        <td class="{{ $tableCell }} text-right text-emerald-700 font-semibold">
                                            {{ $money($d->credit_sum) }}</td>
                                        <td
                                            class="{{ $tableCell }} text-right font-semibold {{ ((float) $d->net) >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                            {{ $money($d->net) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="{{ $tableCell }} py-10 text-center {{ $muted }}">No
                                            daily data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
