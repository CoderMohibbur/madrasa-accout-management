@php
    /** @var \App\Models\Student $student */

    $pageTitle = 'Student Profile';
    $name = $student->full_name ?: ('Student #'.$student->id);

    $money = fn($n) => number_format((float)($n ?? 0), 2);

    // ✅ Controller থেকে না পাঠালে safe fallback
    $feeHistory = $feeHistory ?? collect();     // month-wise fee summary rows
    $txs = $txs ?? null;                        // paginator (student transactions)

    // ✅ Date range fallbacks (controller supports from/to)
    $from = $from ?? now()->startOfMonth();
    $to   = $to ?? now()->endOfMonth();

    // ✅ input[type=date] value safe
    try {
        $fromVal = \Illuminate\Support\Carbon::parse($from)->toDateString();
    } catch (\Throwable $e) {
        $fromVal = now()->startOfMonth()->toDateString();
    }

    try {
        $toVal = \Illuminate\Support\Carbon::parse($to)->toDateString();
    } catch (\Throwable $e) {
        $toVal = now()->endOfMonth()->toDateString();
    }

    // ✅ Summary numbers fallback (controller sends these)
    $feeTotalPaid   = $feeTotalPaid   ?? 0;
    $feePaidInRange = $feePaidInRange ?? 0;
    $feeTotalDue    = $feeTotalDue    ?? 0;
    $feeDueInRange  = $feeDueInRange  ?? 0;

    // Transaction Center route (safe)
    $txCenterUrl = null;
    if (\Illuminate\Support\Facades\Route::has('transactions.center')) {
        $txCenterUrl = route('transactions.center');
    } elseif (\Illuminate\Support\Facades\Route::has('transaction-center.index')) {
        $txCenterUrl = route('transaction-center.index');
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('students.index') }}"
                       class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                        ← Back
                    </a>

                    <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                        {{ $pageTitle }}
                    </h2>
                </div>

                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Student info • boarding • fee history • transaction history
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('students.edit', $student) }}"
                   class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Edit
                </a>

                @if($txCenterUrl)
                    <a href="{{ $txCenterUrl }}?student_id={{ $student->id }}"
                       class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                        Transaction Center
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Hero --}}
            <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-gradient-to-r from-slate-900 to-slate-700 p-5 text-white shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-12 w-12 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center shrink-0 overflow-hidden">
                            @php
                                $photoUrl = $student->photo ? asset('storage/'.$student->photo) : null;
                            @endphp

                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" alt="photo" class="h-full w-full object-cover">
                            @else
                                <span class="text-lg font-bold">
                                    {{ strtoupper(mb_substr(trim($name), 0, 1)) }}
                                </span>
                            @endif
                        </div>

                        <div class="min-w-0">
                            <div class="text-xl font-semibold truncate">{{ $name }}</div>
                            <div class="text-xs opacity-80 mt-1">
                                ID: #{{ $student->id }}
                                • Roll: {{ $student->roll ?? '-' }}
                                • Mobile: {{ $student->mobile ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        {{-- Active --}}
                        @if($student->isActived)
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 border border-emerald-400/20 px-3 py-1 text-xs font-semibold">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span> Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-500/15 border border-rose-400/20 px-3 py-1 text-xs font-semibold">
                                <span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span> Inactive
                            </span>
                        @endif

                        {{-- Boarding --}}
                        @if($student->is_boarding)
                            <span class="inline-flex items-center gap-1 rounded-full bg-white/10 border border-white/10 px-3 py-1 text-xs font-semibold">
                                🛏️ Boarding
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-white/10 border border-white/10 px-3 py-1 text-xs font-semibold">
                                🚶 Non-Boarding
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-4 shadow-sm">
                    <div class="text-[11px] text-slate-500 dark:text-slate-400">Total Paid (All time)</div>
                    <div class="mt-1 text-lg font-bold text-slate-900 dark:text-slate-100">{{ $money($feeTotalPaid) }}</div>
                </div>

                <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-4 shadow-sm">
                    <div class="text-[11px] text-slate-500 dark:text-slate-400">Paid (Selected range)</div>
                    <div class="mt-1 text-lg font-bold text-emerald-700 dark:text-emerald-300">{{ $money($feePaidInRange) }}</div>
                    <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">{{ $fromVal }} → {{ $toVal }}</div>
                </div>

                <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-4 shadow-sm">
                    <div class="text-[11px] text-slate-500 dark:text-slate-400">Total Due (All time)</div>
                    <div class="mt-1 text-lg font-bold text-rose-700 dark:text-rose-300">{{ $money($feeTotalDue) }}</div>
                </div>

                <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-4 shadow-sm">
                    <div class="text-[11px] text-slate-500 dark:text-slate-400">Due (Selected range)</div>
                    <div class="mt-1 text-lg font-bold text-rose-700 dark:text-rose-300">{{ $money($feeDueInRange) }}</div>
                    <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">{{ $fromVal }} → {{ $toVal }}</div>
                </div>
            </div>

            {{-- Content Grid --}}
            <div class="grid grid-cols-12 gap-3">

                {{-- Left: Student Info --}}
                <div class="col-span-12 lg:col-span-5 space-y-3">

                    {{-- Basic Info --}}
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200 dark:border-white/10">
                            <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Basic Information</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Personal details</div>
                        </div>

                        <div class="p-4 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3">
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">Full Name</div>
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $student->full_name ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3">
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">Father Name</div>
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $student->father_name ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3">
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">DOB</div>
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $student->dob ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3">
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">Age</div>
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $student->age ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3">
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">Email</div>
                                <div class="font-semibold text-slate-900 dark:text-slate-100 truncate">{{ $student->email ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3">
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">Mobile</div>
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $student->mobile ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3 col-span-2">
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">Address</div>
                                <div class="font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $student->address ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Academic --}}
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200 dark:border-white/10">
                            <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Academic</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Year • Class • Section • Fees type</div>
                        </div>

                        <div class="p-4 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3">
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">Academic Year</div>
                                <div class="font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $student->academicYear->academic_years ?? $student->academicYear->year ?? '-' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3">
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">Roll</div>
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $student->roll ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3">
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">Class</div>
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $student->class->name ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3">
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">Section</div>
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $student->section->name ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3">
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">Fees Type</div>
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $student->feesType->name ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3">
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">Scholarship</div>
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $money($student->scholarship_amount) }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Boarding --}}
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200 dark:border-white/10">
                            <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Boarding</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Hostel status & dates</div>
                        </div>

                        <div class="p-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <div class="text-slate-600 dark:text-slate-300">Status</div>
                                @if($student->is_boarding)
                                    <span class="inline-flex rounded-full bg-emerald-50 text-emerald-800 px-2.5 py-1 text-xs font-semibold
                                                 dark:bg-emerald-900/20 dark:text-emerald-200 dark:border dark:border-emerald-900/40">
                                        Boarding
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 text-slate-700 px-2.5 py-1 text-xs font-semibold
                                                 dark:bg-slate-800 dark:text-slate-200">
                                        No
                                    </span>
                                @endif
                            </div>

                            @if($student->is_boarding)
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3">
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">Start Date</div>
                                        <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $student->boarding_start_date ?? '-' }}</div>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3">
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">End Date</div>
                                        <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $student->boarding_end_date ?? '-' }}</div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 dark:border-white/10 p-3">
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400">Note</div>
                                    <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $student->boarding_note ?? '-' }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- Right: Fee history + Transactions --}}
                <div class="col-span-12 lg:col-span-7 space-y-3">

                    {{-- Month-wise Fee History --}}
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200 dark:border-white/10 flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Fee History (Month-wise)</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">Collected fee summary by month</div>
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                Total Paid:
                                <span class="font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $money($feeHistory->sum(fn($r)=> (float) data_get($r,'total_paid', data_get($r,'paid', 0)))) }}
                                </span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-200 text-xs uppercase">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Month</th>
                                        <th class="px-4 py-3 text-left">Last Payment Date</th>
                                        <th class="px-4 py-3 text-right">Paid</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                                    @forelse($feeHistory as $row)
                                        @php
                                            $monthName = data_get($row,'month.name')
                                                ?? data_get($row,'month.month_name')
                                                ?? data_get($row,'month_title')
                                                ?? ('Month #'.(data_get($row,'months_id') ?? data_get($row,'month_id') ?? ''));
                                            $lastDate = data_get($row,'last_date') ?? data_get($row,'transactions_date') ?? '-';
                                            $paid = (float) (data_get($row,'total_paid') ?? data_get($row,'paid') ?? 0);
                                        @endphp
                                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/60">
                                            <td class="px-4 py-3 font-semibold text-slate-900 dark:text-slate-100">{{ $monthName }}</td>
                                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300 whitespace-nowrap">{{ $lastDate }}</td>
                                            <td class="px-4 py-3 text-right font-semibold text-emerald-700 dark:text-emerald-300">{{ $money($paid) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-10 text-center text-slate-500 dark:text-slate-400">
                                                No fee history found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Transaction History --}}
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200 dark:border-white/10">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Transaction History</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">All transactions of this student</div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-end gap-2">
                                    <form method="GET" action="{{ route('students.show', $student) }}" class="flex items-end gap-2 flex-wrap">
                                        <div>
                                            <label class="block text-[11px] text-slate-500 dark:text-slate-400">From</label>
                                            <input type="date" name="from" value="{{ $fromVal }}"
                                                   class="rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                                        </div>

                                        <div>
                                            <label class="block text-[11px] text-slate-500 dark:text-slate-400">To</label>
                                            <input type="date" name="to" value="{{ $toVal }}"
                                                   class="rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                                        </div>

                                        <button type="submit"
                                                class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                                            Apply
                                        </button>

                                        <a href="{{ route('students.show', $student) }}"
                                           class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                            Reset
                                        </a>
                                    </form>

                                    @if($txCenterUrl)
                                        <a href="{{ $txCenterUrl }}?student_id={{ $student->id }}"
                                           class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                            + Add Transaction
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-200 text-xs uppercase">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Date</th>
                                        <th class="px-4 py-3 text-left">Type</th>
                                        <th class="px-4 py-3 text-left">Note</th>
                                        <th class="px-4 py-3 text-right">Debit</th>
                                        <th class="px-4 py-3 text-right">Credit</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                                    @php
                                        $rows = $txs ? $txs : collect();
                                    @endphp

                                    @forelse($rows as $tx)
                                        @php
                                            $typeName = data_get($tx,'type.name') ?? ('Type #'.($tx->transactions_type_id ?? ''));
                                            $note = $tx->note ?? $tx->remarks ?? $tx->title ?? '-';
                                            $debit = (float)($tx->debit ?? 0);
                                            $credit = (float)($tx->credit ?? 0);
                                        @endphp

                                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/60">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="font-medium text-slate-900 dark:text-slate-100">{{ $tx->transactions_date ?? '-' }}</div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">#{{ $tx->id }}</div>
                                            </td>

                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-2 py-1 text-xs">
                                                    {{ $typeName }}
                                                </span>
                                            </td>

                                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                                {{ $note }}
                                            </td>

                                            <td class="px-4 py-3 text-right font-semibold {{ $debit > 0 ? 'text-rose-700 dark:text-rose-300' : 'text-slate-400' }}">
                                                {{ $debit > 0 ? $money($debit) : '-' }}
                                            </td>

                                            <td class="px-4 py-3 text-right font-semibold {{ $credit > 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-slate-400' }}">
                                                {{ $credit > 0 ? $money($credit) : '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-10 text-center text-slate-500 dark:text-slate-400">
                                                No transactions found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if($txs && method_exists($txs, 'links'))
                            <div class="border-t border-slate-200 dark:border-white/10 bg-slate-50/60 dark:bg-slate-800/40 px-4 py-3">
                                {{ $txs->links() }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>