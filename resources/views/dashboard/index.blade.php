@php
    $pageTitle = 'Dashboard';

    $money = fn($n) => number_format((float) $n, 2);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $pageTitle }}</h2>
                <p class="text-xs text-slate-500 mt-1">Bank balance • fees • expense • loan • reports</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('transactions.center') }}"
                    class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    Transaction Center
                </a>

                <a href="{{ route('reports.transactions') }}"
                    class="inline-flex items-center rounded-xl bg-slate-900 px-3 py-2 text-sm text-white hover:bg-slate-800">
                    Reports
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Hero Summary --}}
            <div
                class="rounded-3xl border border-slate-200 bg-gradient-to-r from-slate-900 to-slate-700 p-5 text-white shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="text-sm/6 opacity-80">Selected range</div>
                        <div class="text-xl font-semibold">
                            {{ $from }} → {{ $to }}
                        </div>
                        <div class="text-xs opacity-80 mt-1">
                            Transactions: {{ number_format((int) $transactionsCount) }}
                            @if ($accountId)
                                • Account filtered
                            @endif
                            @if ($typeId)
                                • Type filtered
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 w-full sm:w-auto">
                        <div class="rounded-2xl bg-white/10 border border-white/10 p-3">
                            <div class="text-[11px] opacity-80">Total In</div>
                            <div class="text-lg font-semibold">{{ $money($totalDebit) }}</div>
                        </div>
                        <div class="rounded-2xl bg-white/10 border border-white/10 p-3">
                            <div class="text-[11px] opacity-80">Total Out</div>
                            <div class="text-lg font-semibold">{{ $money($totalCredit) }}</div>
                        </div>
                        <div class="rounded-2xl bg-white/10 border border-white/10 p-3">
                            <div class="text-[11px] opacity-80">Net</div>
                            <div class="text-lg font-semibold">{{ $money($net) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('dashboard') }}"
                class="bg-white border border-slate-200 rounded-3xl shadow-sm p-4">
                <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">From</label>
                        <input type="date" name="from" value="{{ $from }}"
                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                    </div>

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">To</label>
                        <input type="date" name="to" value="{{ $to }}"
                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                    </div>

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Account</label>
                        <select name="account_id"
                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                            <option value="">All accounts</option>
                            @foreach ($accounts ?? [] as $acc)
                                <option value="{{ $acc->id }}" @selected((string) $acc->id === (string) $accountId)>
                                    {{ $acc->name ?? 'Account #' . $acc->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-2">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Type</label>
                        <select name="type_id"
                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                            <option value="">All types</option>
                            @foreach ($types ?? [] as $t)
                                <option value="{{ $t->id }}" @selected((string) $t->id === (string) $typeId)>
                                    {{ $t->name ?? 'Type #' . $t->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-1 flex items-end">
                        <button type="submit"
                            class="w-full rounded-2xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                            Go
                        </button>
                    </div>
                </div>
            </form>


            {{-- KPI Cards (3 per row on large screens) --}}
            @php
                $totalBalance = collect($accountBalances ?? [])->sum(fn($r) => (float) ($r->balance ?? 0));
            @endphp

            <div class="grid grid-cols-12 gap-3">

                {{-- Students --}}
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="absolute inset-x-0 top-0 h-1 bg-indigo-500/70"></div>
                        <div class="p-4 pt-5">
                            <div class="text-xs text-slate-500">Students</div>
                            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($totalStudents) }}
                            </div>
                            <div class="mt-2 text-[11px] text-slate-500">
                                Paid (range): <span
                                    class="font-semibold text-slate-700">{{ number_format($studentsPaidCount) }}</span>
                                • Unpaid (approx): <span
                                    class="font-semibold text-slate-700">{{ number_format($studentsUnpaidApprox) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fees Collected --}}
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="absolute inset-x-0 top-0 h-1 bg-emerald-500/70"></div>
                        <div class="p-4 pt-5">
                            <div class="text-xs text-slate-500">Fees Collected</div>
                            <div class="mt-1 text-2xl font-semibold text-emerald-700">
                                {{ number_format((float) $feesCollected, 2) }}</div>
                            <div class="mt-2 text-[11px] text-slate-500">Student fee debit sum</div>
                        </div>
                    </div>
                </div>

                {{-- Expense --}}
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="absolute inset-x-0 top-0 h-1 bg-rose-500/70"></div>
                        <div class="p-4 pt-5">
                            <div class="text-xs text-slate-500">Total Expense</div>
                            <div class="mt-1 text-2xl font-semibold text-rose-700">
                                {{ number_format((float) $expenseTotal, 2) }}</div>
                            <div class="mt-2 text-[11px] text-slate-500">Expense credit sum</div>
                        </div>
                    </div>
                </div>

                {{-- Income --}}
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="absolute inset-x-0 top-0 h-1 bg-sky-500/70"></div>
                        <div class="p-4 pt-5">
                            <div class="text-xs text-slate-500">Total Income</div>
                            <div class="mt-1 text-2xl font-semibold text-emerald-700">
                                {{ number_format((float) $incomeTotal, 2) }}</div>
                            <div class="mt-2 text-[11px] text-slate-500">Income debit sum</div>
                        </div>
                    </div>
                </div>

                {{-- Donation --}}
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="absolute inset-x-0 top-0 h-1 bg-amber-500/70"></div>
                        <div class="p-4 pt-5">
                            <div class="text-xs text-slate-500">Donation</div>
                            <div class="mt-1 text-2xl font-semibold text-emerald-700">
                                {{ number_format((float) $donationTotal, 2) }}</div>
                            <div class="mt-2 text-[11px] text-slate-500">Donation debit sum</div>
                        </div>
                    </div>
                </div>

                {{-- Loan Outstanding --}}
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="absolute inset-x-0 top-0 h-1 bg-slate-500/70"></div>
                        <div class="p-4 pt-5">
                            <div class="text-xs text-slate-500">Loan Outstanding</div>
                            <div class="mt-1 text-2xl font-semibold text-slate-900">
                                {{ number_format((float) $loanOutstanding, 2) }}</div>
                            <div class="mt-2 text-[11px] text-slate-500">
                                Taken {{ number_format((float) $loanTakenTotal, 2) }} − Repay
                                {{ number_format((float) $loanRepaymentTotal, 2) }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Bank Balance (sum of all account balances) --}}
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="absolute inset-x-0 top-0 h-1 bg-violet-500/70"></div>
                        <div class="p-4 pt-5">
                            <div class="text-xs text-slate-500">Total Bank Balance</div>
                            <div
                                class="mt-1 text-2xl font-semibold {{ $totalBalance >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ number_format((float) $totalBalance, 2) }}
                            </div>
                            <div class="mt-2 text-[11px] text-slate-500">Sum of all accounts (within filter)</div>
                        </div>
                    </div>
                </div>

                {{-- Net (Debit - Credit) --}}
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div
                            class="absolute inset-x-0 top-0 h-1 {{ $net >= 0 ? 'bg-emerald-500/70' : 'bg-rose-500/70' }}">
                        </div>
                        <div class="p-4 pt-5">
                            <div class="text-xs text-slate-500">Net (Debit − Credit)</div>
                            <div
                                class="mt-1 text-2xl font-semibold {{ $net >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ number_format((float) $net, 2) }}
                            </div>

                            @php
                                $totalFlow = max(1, (float) $totalDebit + (float) $totalCredit);
                                $debitPct = ((float) $totalDebit / $totalFlow) * 100;
                                $creditPct = ((float) $totalCredit / $totalFlow) * 100;
                            @endphp

                            <div class="mt-3">
                                <div class="text-[11px] text-slate-500 mb-1">In vs Out</div>
                                <div class="h-2 rounded-full bg-slate-100 overflow-hidden border border-slate-200">
                                    <div class="h-2 bg-emerald-500/70" style="width: {{ $debitPct }}%"></div>
                                </div>
                                <div
                                    class="mt-2 h-2 rounded-full bg-slate-100 overflow-hidden border border-slate-200">
                                    <div class="h-2 bg-rose-500/70" style="width: {{ $creditPct }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Transactions count --}}
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="absolute inset-x-0 top-0 h-1 bg-teal-500/70"></div>
                        <div class="p-4 pt-5">
                            <div class="text-xs text-slate-500">Transactions (Range)</div>
                            <div class="mt-1 text-2xl font-semibold text-slate-900">
                                {{ number_format((int) $transactionsCount) }}</div>
                            <div class="mt-2 text-[11px] text-slate-500">All types included (based on filter)</div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ✅ Extra Insights (Fees Due + Loan + Expense Category) --}}
            <div class="grid grid-cols-12 gap-4">

                {{-- Fees Due --}}
                <div class="col-span-12 lg:col-span-4">
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="p-4 border-b border-slate-200 bg-slate-50/60">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">Fees Due</div>
                                    <div class="text-xs text-slate-500">Selected range এ যারা fee দেয়নি</div>
                                </div>

                                <div class="text-right">
                                    <div class="text-[11px] text-slate-500">Due Students</div>
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

                            <div class="mt-4 grid grid-cols-3 gap-2">
                                <div class="rounded-2xl border border-slate-200 bg-white p-3">
                                    <div class="text-[11px] text-slate-500">Total</div>
                                    <div class="text-sm font-semibold text-slate-900">
                                        {{ number_format((int) ($totalStudents ?? 0)) }}</div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-3">
                                    <div class="text-[11px] text-slate-500">Paid</div>
                                    <div class="text-sm font-semibold text-emerald-700">{{ number_format($paid) }}
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-3">
                                    <div class="text-[11px] text-slate-500">Due</div>
                                    <div class="text-sm font-semibold text-rose-700">
                                        {{ number_format((int) ($studentsDueCount ?? 0)) }}</div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1">
                                    <span>Paid progress</span>
                                    <span class="font-semibold text-slate-700">{{ (int) $paidPct }}%</span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-100 overflow-hidden border border-slate-200">
                                    <div class="h-2 bg-emerald-500/70" style="width: {{ $paidPct }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="text-xs font-semibold text-slate-800 mb-2">Top due students</div>

                            <div class="space-y-2">
                                @forelse(($dueStudents ?? []) as $s)
                                    @php
                                        $nm = $s->student_name ?? 'Student #' . $s->id;
                                        $initial = strtoupper(mb_substr(trim($nm), 0, 1));
                                    @endphp

                                    <div
                                        class="flex items-center justify-between rounded-2xl border border-slate-200 px-3 py-2 hover:bg-slate-50">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div
                                                class="h-9 w-9 shrink-0 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-semibold">
                                                {{ $initial }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-sm font-medium text-slate-900 truncate">
                                                    {{ $nm }}</div>
                                                <div class="text-[11px] text-slate-500">ID: {{ $s->id }}</div>
                                            </div>
                                        </div>

                                        @if (\Illuminate\Support\Facades\Route::has('students.show'))
                                            <a href="{{ route('students.show', $s->id) }}"
                                                class="text-xs font-semibold text-slate-700 hover:underline">
                                                View
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400">View</span>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-sm text-slate-500">No due students 🎉</div>
                                @endforelse
                            </div>

                            @if (\Illuminate\Support\Facades\Route::has('students.index'))
                                <div class="mt-3">
                                    <a href="{{ route('students.index') }}"
                                        class="text-xs text-slate-700 hover:underline">View all students</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Loan Overview --}}
                <div class="col-span-12 lg:col-span-4">
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="p-4 border-b border-slate-200 bg-slate-50/60">
                            <div class="text-sm font-semibold text-slate-900">Loan Overview</div>
                            <div class="text-xs text-slate-500">Taken vs Paid vs Outstanding</div>

                            @php
                                $loanTaken = (float) ($loanTakenTotal ?? 0);
                                $loanPaid = (float) ($loanRepaymentTotal ?? 0);
                                $loanOut = (float) ($loanOutstanding ?? 0);
                                $loanPct = (float) ($loanPaidPct ?? 0);
                            @endphp

                            <div class="mt-4 grid grid-cols-3 gap-2">
                                <div class="rounded-2xl border border-slate-200 bg-white p-3">
                                    <div class="text-[11px] text-slate-500">Taken</div>
                                    <div class="text-sm font-semibold text-slate-900">{{ $money($loanTaken) }}</div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-3">
                                    <div class="text-[11px] text-slate-500">Paid</div>
                                    <div class="text-sm font-semibold text-emerald-700">{{ $money($loanPaid) }}</div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-3">
                                    <div class="text-[11px] text-slate-500">Outstanding</div>
                                    <div class="text-sm font-semibold text-rose-700">{{ $money($loanOut) }}</div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1">
                                    <span>Paid progress</span>
                                    <span class="font-semibold text-slate-700">{{ (int) $loanPct }}%</span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-100 overflow-hidden border border-slate-200">
                                    <div class="h-2 bg-emerald-500/70" style="width: {{ $loanPct }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="rounded-2xl border border-slate-200 bg-white p-3">
                                <div class="text-xs text-slate-500">Note</div>
                                <div class="text-sm text-slate-800">Outstanding = Taken − Paid</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Expense Breakdown --}}
                <div class="col-span-12 lg:col-span-4">
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div
                            class="p-4 border-b border-slate-200 bg-slate-50/60 flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-slate-900">Expense Breakdown</div>
                                <div class="text-xs text-slate-500">কিসের জন্য কত খরচ হয়েছে</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[11px] text-slate-500">Total</div>
                                <div class="text-sm font-semibold text-rose-700">{{ $money($expenseTotal ?? 0) }}
                                </div>
                            </div>
                        </div>

                        <div class="p-4 space-y-3">
                            @php $expTotal = max(1, (float)($expenseTotal ?? 0)); @endphp

                            @forelse(($expenseByCategory ?? []) as $row)
                                @php
                                    $cat = $row->category_name ?? 'Uncategorized';
                                    $amt = (float) ($row->total ?? 0);
                                    $pct = min(100, ($amt / $expTotal) * 100);
                                @endphp

                                <div class="rounded-2xl border border-slate-200 p-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="text-sm font-medium text-slate-900 truncate">{{ $cat }}
                                        </div>
                                        <div class="text-sm font-semibold text-rose-700">{{ $money($amt) }}</div>
                                    </div>
                                    <div
                                        class="mt-2 h-2 rounded-full bg-slate-100 overflow-hidden border border-slate-200">
                                        <div class="h-2 bg-rose-500/70" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <div class="text-sm font-semibold text-slate-800">Breakdown পাওয়া যায়নি</div>
                                    <div class="text-xs text-slate-500 mt-1">
                                        `transactions.catagory_id` অথবা `transactions.expens_id` + related tables না
                                        থাকলে breakdown হবে না।
                                    </div>
                                </div>
                            @endforelse

                            @if (\Illuminate\Support\Facades\Route::has('expens.index'))
                                <div>
                                    <a href="{{ route('expens.index') }}"
                                        class="text-xs text-slate-700 hover:underline">View all expenses</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>



            {{-- Bank / Account balances + Recent --}}
            <div class="grid grid-cols-12 gap-3">

                {{-- Account Balances --}}
                <div
                    class="col-span-12 lg:col-span-5 bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200">
                        <div class="text-sm font-semibold text-slate-900">Bank / Account Balance</div>
                        <div class="text-xs text-slate-500">Debit − Credit (within selected filter)</div>
                    </div>

                    <div class="p-4 space-y-3">
                        @forelse($accountBalances ?? [] as $row)
                            @php
                                $bal = (float) $row->balance;
                                $pct = min(100, (abs($bal) / (float) $maxAbsBalance) * 100);
                            @endphp
                            <div class="rounded-2xl border border-slate-200 p-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $row->account_name }}</div>
                                        <div class="text-[11px] text-slate-500">
                                            In: {{ $money($row->debit_sum) }} • Out: {{ $money($row->credit_sum) }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div
                                            class="text-sm font-bold {{ $bal >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                            {{ $money($bal) }}
                                        </div>
                                        <div class="text-[11px] text-slate-500">Balance</div>
                                    </div>
                                </div>

                                <div
                                    class="mt-3 h-2 rounded-full bg-slate-100 overflow-hidden border border-slate-200">
                                    <div class="h-2 {{ $bal >= 0 ? 'bg-emerald-500/70' : 'bg-rose-500/70' }}"
                                        style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-slate-500">No account transactions found in this range.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Transactions --}}
                <div
                    class="col-span-12 lg:col-span-7 bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">Recent Transactions</div>
                            <div class="text-xs text-slate-500">Latest 10 (within filter)</div>
                        </div>
                        <a href="{{ route('transactions.center') }}"
                            class="text-xs text-slate-700 hover:underline">View all</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-3 text-left">Date</th>
                                    <th class="px-4 py-3 text-left">Type</th>
                                    <th class="px-4 py-3 text-left">Party</th>
                                    <th class="px-4 py-3 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($recent ?? [] as $tx)
                                    @php
                                        $typeName = data_get($tx, 'type.name') ?? 'Type #' . $tx->transactions_type_id;

                                        $studentName =
                                            data_get($tx, 'student.name') ?? data_get($tx, 'student.student_name');
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
                                        $amount = $d > 0 ? $d : $c;
                                        $isDebit = $d > 0;
                                    @endphp

                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="font-medium text-slate-900">
                                                {{ $tx->transactions_date ?? '-' }}</div>
                                            <div class="text-xs text-slate-500">#{{ $tx->id }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-2 py-1 text-xs">
                                                {{ $typeName }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">{{ $party }}</td>
                                        <td
                                            class="px-4 py-3 text-right font-semibold {{ $isDebit ? 'text-emerald-700' : 'text-rose-700' }}">
                                            {{ $money($amount) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-slate-500">
                                            No transactions found in this range.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- Type Summary + Daily --}}
            <div class="grid grid-cols-12 gap-3">

                {{-- Type Summary --}}
                <div
                    class="col-span-12 lg:col-span-5 bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200">
                        <div class="text-sm font-semibold text-slate-900">Type Summary</div>
                        <div class="text-xs text-slate-500">Debit / Credit totals</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-3 text-left">Type</th>
                                    <th class="px-4 py-3 text-right">Debit</th>
                                    <th class="px-4 py-3 text-right">Credit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($typeBreakdown ?? [] as $row)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-slate-900">{{ $row->type_name }}</div>
                                            <div class="text-xs text-slate-500">{{ $row->count }} tx</div>
                                        </td>
                                        <td class="px-4 py-3 text-right text-emerald-700 font-semibold">
                                            {{ $money($row->debit_sum) }}</td>
                                        <td class="px-4 py-3 text-right text-rose-700 font-semibold">
                                            {{ $money($row->credit_sum) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-10 text-center text-slate-500">No data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Daily Summary --}}
                <div
                    class="col-span-12 lg:col-span-7 bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200">
                        <div class="text-sm font-semibold text-slate-900">Daily Summary</div>
                        <div class="text-xs text-slate-500">Net per day</div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-3 text-left">Date</th>
                                    <th class="px-4 py-3 text-right">Debit</th>
                                    <th class="px-4 py-3 text-right">Credit</th>
                                    <th class="px-4 py-3 text-right">Net</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($daily ?? [] as $d)
                                    <tr>
                                        <td class="px-4 py-3">{{ $d->d }}</td>
                                        <td class="px-4 py-3 text-right text-emerald-700 font-semibold">
                                            {{ $money($d->debit_sum) }}</td>
                                        <td class="px-4 py-3 text-right text-rose-700 font-semibold">
                                            {{ $money($d->credit_sum) }}</td>
                                        <td
                                            class="px-4 py-3 text-right font-semibold {{ ((float) $d->net) >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                            {{ $money($d->net) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-slate-500">No daily data
                                        </td>
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
