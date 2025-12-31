{{-- resources/views/transactions/center.blade.php --}}
@php
    $pageTitle = 'Transaction Center';

    // Helpers (safe)
    $getAmount = function ($tx) {
        $d = (float) ($tx->debit ?? 0);
        $c = (float) ($tx->credit ?? 0);
        return $d > 0 ? $d : $c;
    };

    $getParty = function ($tx) {
        // Relationship এখনো confirm না—safe fallback
        // If you later add relations: student/donor/lender -> it will auto show.
        $studentName =
            data_get($tx, 'student.name') ??
            (data_get($tx, 'student.student_name') ?? data_get($tx, 'student.full_name'));

        $donorName =
            data_get($tx, 'donor.name') ?? (data_get($tx, 'donor.donor_name') ?? data_get($tx, 'donor.doner_name'));

        $lenderName = data_get($tx, 'lender.name') ?? data_get($tx, 'lender.lender_name');

        if ($studentName) {
            return ['label' => 'Student', 'name' => $studentName];
        }
        if ($donorName) {
            return ['label' => 'Donor', 'name' => $donorName];
        }
        if ($lenderName) {
            return ['label' => 'Lender', 'name' => $lenderName];
        }

        // Fallback by IDs (no relation)
        if (!empty($tx->student_id)) {
            return ['label' => 'Student', 'name' => 'ID: ' . $tx->student_id];
        }
        if (!empty($tx->doner_id)) {
            return ['label' => 'Donor', 'name' => 'ID: ' . $tx->doner_id];
        }
        if (!empty($tx->lender_id)) {
            return ['label' => 'Lender', 'name' => 'ID: ' . $tx->lender_id];
        }

        return ['label' => '-', 'name' => '-'];
    };

    // ✅ Phase 2.6: Active tab + per-tab old value helpers
    $activeTab = old('type_key', 'student_fee');

    $oldFor = function ($key, $tab, $default = '') use ($activeTab) {
        return $activeTab === $tab ? old($key, $default) : $default;
    };

    $oldSelected = function ($key, $value, $tab) use ($activeTab) {
        return $activeTab === $tab && (string) old($key) === (string) $value;
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $pageTitle }}</h2>
                <p class="text-xs text-slate-500 mt-1">All transactions in one place — filter + review + quick add</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ url('/chart-of-accounts') }}"
                    class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    Chart of Accounts
                </a>
                <a href="{{ url('/list-student-fees') }}"
                    class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    Fees List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Layout: main + right panel --}}
            <div class="grid grid-cols-12 gap-4">

                {{-- MAIN (Filters + Table) --}}
                <section class="col-span-12 lg:col-span-8">

                    @if (session('success'))
                        <div
                            class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Filters bar --}}
                    <form method="GET" action="{{ url('/transaction-center') }}" data-filter-form
                        class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-slate-800">Filters</h3>

                                <div class="flex items-center gap-2">
                                    <button type="button" data-range="today"
                                        class="text-xs rounded-lg border border-slate-200 bg-white px-2 py-1 hover:bg-slate-50">
                                        Today
                                    </button>
                                    <button type="button" data-range="month"
                                        class="text-xs rounded-lg border border-slate-200 bg-white px-2 py-1 hover:bg-slate-50">
                                        This Month
                                    </button>

                                    <a href="{{ url('/transaction-center') }}"
                                        class="text-xs rounded-lg border border-slate-200 bg-white px-2 py-1 hover:bg-slate-50">
                                        Reset
                                    </a>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-3">
                                <div class="col-span-12 sm:col-span-3">
                                    <label class="block text-xs font-medium text-slate-600 mb-1">From</label>
                                    <input type="date" name="from" value="{{ request('from') }}"
                                        class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                </div>

                                <div class="col-span-12 sm:col-span-3">
                                    <label class="block text-xs font-medium text-slate-600 mb-1">To</label>
                                    <input type="date" name="to" value="{{ request('to') }}"
                                        class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                </div>

                                <div class="col-span-12 sm:col-span-3">
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Account</label>
                                    <select name="account_id"
                                        class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        <option value="">All accounts</option>
                                        @foreach ($accounts ?? [] as $acc)
                                            <option value="{{ $acc->id }}" @selected((string) $acc->id === (string) request('account_id'))>
                                                {{ $acc->name ?? 'Account #' . $acc->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-12 sm:col-span-3">
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Type</label>
                                    <select name="type_id"
                                        class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        <option value="">All types</option>
                                        @foreach ($types ?? [] as $t)
                                            <option value="{{ $t->id }}" @selected((string) $t->id === (string) request('type_id'))>
                                                {{ $t->name ?? 'Type #' . $t->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-12 sm:col-span-9">
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Search</label>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Receipt no / note / keyword…"
                                        class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                </div>

                                <div class="col-span-12 sm:col-span-3 flex items-end">
                                    <button type="submit"
                                        class="w-full rounded-xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                                        Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <div class="mt-4 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200">
                            <h3 class="text-sm font-semibold text-slate-800">Transactions</h3>
                            <div class="text-xs text-slate-500">
                                Showing
                                {{ method_exists($transactions ?? null, 'total') ? $transactions->total() : (is_countable($transactions ?? []) ? count($transactions) : 0) }}
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Date</th>
                                        <th class="px-4 py-3 text-left">Type</th>
                                        <th class="px-4 py-3 text-left">Party</th>
                                        <th class="px-4 py-3 text-left">Account</th>
                                        <th class="px-4 py-3 text-right">Amount</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse(($transactions ?? []) as $tx)
                                        @php
                                            $party = $getParty($tx);
                                            $amount = $getAmount($tx);

                                            $typeName =
                                                data_get($tx, 'type.name') ??
                                                (data_get($tx, 'transactionsType.name') ??
                                                    (data_get($tx, 'transactions_type.name') ??
                                                        ($tx->transactions_type_id ?? null
                                                            ? 'Type #' . $tx->transactions_type_id
                                                            : '-')));

                                            $accountName =
                                                data_get($tx, 'account.name') ??
                                                ($tx->account_id ?? null ? 'Account #' . $tx->account_id : '-');

                                            $isDebit = ((float) ($tx->debit ?? 0)) > 0;
                                        @endphp
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="font-medium text-slate-800">
                                                    {{ $tx->transactions_date ?? '-' }}
                                                </div>
                                                <div class="text-xs text-slate-500">
                                                    #{{ $tx->id }}
                                                </div>
                                            </td>

                                            <td class="px-4 py-3">
                                                <span
                                                    class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs">
                                                    {{ $typeName }}
                                                </span>
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="font-medium text-slate-800">
                                                    {{ $party['name'] }}
                                                </div>
                                                <div class="text-xs text-slate-500">{{ $party['label'] }}</div>
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="text-slate-800">{{ $accountName }}</div>
                                                <div class="text-xs text-slate-500">
                                                    {{ $tx->recipt_no ? 'Receipt: ' . $tx->recipt_no : ($tx->student_book_number ? 'Book: ' . $tx->student_book_number : '') }}
                                                </div>
                                            </td>

                                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                                <div
                                                    class="font-semibold {{ $isDebit ? 'text-emerald-700' : 'text-rose-700' }}">
                                                    {{ number_format((float) $amount, 2) }}
                                                </div>
                                                <div class="text-xs text-slate-500">
                                                    {{ $isDebit ? 'Debit' : 'Credit' }}
                                                </div>
                                            </td>

                                            {{-- ✅ Phase 2.6: Actions route-safe --}}
                                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center gap-2">
                                                    @if (\Illuminate\Support\Facades\Route::has('transactions.receipt'))
                                                        <a href="{{ route('transactions.receipt', $tx->id) }}"
                                                            class="text-xs rounded-lg border border-slate-200 bg-white px-2 py-1 hover:bg-slate-50">
                                                            Print
                                                        </a>
                                                    @else
                                                        <button type="button" disabled
                                                            class="text-xs rounded-lg border border-slate-200 bg-white px-2 py-1 opacity-50 cursor-not-allowed"
                                                            title="Phase 6 এ Receipt/Print আসবে">
                                                            Print
                                                        </button>
                                                    @endif

                                                    @if (\Illuminate\Support\Facades\Route::has('transactions.edit'))
                                                        <a href="{{ route('transactions.edit', $tx->id) }}"
                                                            class="text-xs rounded-lg border border-slate-200 bg-white px-2 py-1 hover:bg-slate-50">
                                                            Edit
                                                        </a>
                                                    @else
                                                        <button type="button" disabled
                                                            class="text-xs rounded-lg border border-slate-200 bg-white px-2 py-1 opacity-50 cursor-not-allowed"
                                                            title="Phase 5 এ Edit flow আসবে">
                                                            Edit
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                                No transactions found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if (method_exists($transactions ?? null, 'links'))
                            <div class="px-4 py-3 border-t border-slate-200">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    </div>
                </section>

                {{-- RIGHT PANEL (Quick Add) --}}
                <aside class="col-span-12 lg:col-span-4">

                    @php
                        $activeTab = old('type_key', 'student_fee');
                        $feeMode = old('_tc_fee_mode', 'single');
                    @endphp

                    <div x-data="transactionCenterQuickAdd(@js($activeTab), @js($feeMode))"
                        class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200">
                            <h3 class="text-sm font-semibold text-slate-800">Quick Add</h3>
                            <p class="text-xs text-slate-500 mt-1">Add transactions without leaving this page</p>
                        </div>

                        {{-- ✅ Phase 2.6: errors box inside right panel --}}
                        @if ($errors->any())
                            <div
                                class="mx-4 mt-3 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-rose-800 text-xs">
                                <div class="font-semibold mb-1">Fix these:</div>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Tabs --}}
                        <div class="px-3 pt-3">
                            <div class="grid grid-cols-3 gap-2 text-xs">
                                <button type="button" @click="tab='student_fee'"
                                    :class="tab === 'student_fee' ? activeTabClass : tabClass">Student Fee</button>
                                <button type="button" @click="tab='donation'"
                                    :class="tab === 'donation' ? activeTabClass : tabClass">Donation</button>
                                <button type="button" @click="tab='loan_taken'"
                                    :class="tab === 'loan_taken' ? activeTabClass : tabClass">Loan</button>

                                <button type="button" @click="tab='loan_repayment'"
                                    :class="tab === 'loan_repayment' ? activeTabClass : tabClass">Repayment</button>
                                <button type="button" @click="tab='expense'"
                                    :class="tab === 'expense' ? activeTabClass : tabClass">Expense</button>
                                <button type="button" @click="tab='income'"
                                    :class="tab === 'income' ? activeTabClass : tabClass">Income</button>
                            </div>
                        </div>

                        {{-- Forms --}}
                        <div class="p-4 space-y-4">

                            {{-- Student Fee --}}
                            <div x-show="tab==='student_fee'" x-cloak class="space-y-3">

                                {{-- ✅ Sub Tabs (default: single) --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" @click="feeMode='single'"
                                        class="rounded-xl border px-3 py-2 text-xs"
                                        :class="feeMode === 'single' ? 'bg-slate-900 text-white border-slate-900' :
                                            'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                                        Single Student
                                    </button>

                                    <button type="button" @click="feeMode='bulk'"
                                        class="rounded-xl border px-3 py-2 text-xs"
                                        :class="feeMode === 'bulk' ? 'bg-slate-900 text-white border-slate-900' :
                                            'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                                        Bulk Students
                                    </button>
                                </div>

                                {{-- =========================
        SINGLE (Phase 2 Quick Store)
    ========================== --}}
                                <div x-show="feeMode==='single'" x-cloak
                                    class="rounded-xl border border-slate-200 bg-white p-3">
                                    <div class="mb-2">
                                        <div class="text-sm font-semibold text-slate-800">Single Fee</div>
                                        <div class="text-xs text-slate-500">One student fee entry</div>
                                    </div>

                                    <form method="POST" action="{{ url('/transactions/quick-store') }}"
                                        class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="type_key" value="student_fee">
                                        {{-- ✅ keep mode on validation error --}}
                                        <input type="hidden" name="_tc_fee_mode" value="single">

                                        {{-- আপনার existing single form (একদম একই) --}}
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 mb-1">Student</label>
                                            <div class="flex items-center gap-2">
                                                <select id="select_student_fee_student" data-entity-select="students"
                                                    name="student_id"
                                                    class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                                    <option value="">Select student</option>
                                                    @foreach ($students ?? [] as $s)
                                                        @php
                                                            $label =
                                                                $s->name ??
                                                                ($s->student_name ??
                                                                    ($s->full_name ?? 'Student #' . $s->id));
                                                        @endphp
                                                        <option value="{{ $s->id }}"
                                                            @selected($oldSelected('student_id', $s->id, 'student_fee'))>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <button type="button"
                                                    @click="openModal('students', 'select_student_fee_student')"
                                                    class="shrink-0 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs hover:bg-slate-50">
                                                    + Add
                                                </button>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 mb-1">Monthly</label>
                                                <input type="number" step="0.01" name="monthly_fees"
                                                    value="{{ $oldFor('monthly_fees', 'student_fee') }}"
                                                    class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 mb-1">Boarding</label>
                                                <input type="number" step="0.01" name="boarding_fees"
                                                    value="{{ $oldFor('boarding_fees', 'student_fee') }}"
                                                    class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 mb-1">Management</label>
                                                <input type="number" step="0.01" name="management_fees"
                                                    value="{{ $oldFor('management_fees', 'student_fee') }}"
                                                    class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 mb-1">Exam</label>
                                                <input type="number" step="0.01" name="exam_fees"
                                                    value="{{ $oldFor('exam_fees', 'student_fee') }}"
                                                    class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Others</label>
                                            <input type="number" step="0.01" name="others_fees"
                                                value="{{ $oldFor('others_fees', 'student_fee') }}"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        </div>

                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 mb-1">Account</label>
                                            <div class="flex items-center gap-2">
                                                <select id="select_student_fee_account" data-entity-select="accounts"
                                                    name="account_id"
                                                    class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                                    <option value="">Select account</option>
                                                    @foreach ($accounts ?? [] as $acc)
                                                        <option value="{{ $acc->id }}"
                                                            @selected($oldSelected('account_id', $acc->id, 'student_fee'))>
                                                            {{ $acc->name ?? 'Account #' . $acc->id }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <button type="button"
                                                    @click="openModal('accounts', 'select_student_fee_account')"
                                                    class="shrink-0 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs hover:bg-slate-50">
                                                    + Add
                                                </button>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 mb-1">Date</label>
                                                <input type="date" name="transactions_date"
                                                    value="{{ $oldFor('transactions_date', 'student_fee') }}"
                                                    class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-600 mb-1">Receipt
                                                    No</label>
                                                <input type="text" name="recipt_no"
                                                    value="{{ $oldFor('recipt_no', 'student_fee') }}"
                                                    class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Note</label>
                                            <input type="text" name="note"
                                                value="{{ $oldFor('note', 'student_fee') }}"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        </div>

                                        <button type="submit"
                                            class="w-full rounded-xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                                            Save Fee
                                        </button>
                                    </form>
                                </div>

                                {{-- =========================
        BULK (Phase 4)
    ========================== --}}
                                <div x-show="feeMode==='bulk'" x-cloak
                                    class="rounded-xl border border-slate-200 bg-white p-3">
                                    <div class="mb-2">
                                        <div class="text-sm font-semibold text-slate-800">Bulk Fees</div>
                                        <div class="text-xs text-slate-500">Class/Section/Month wise bulk entry</div>
                                    </div>

                                    @include('transactions.partials.forms.student_fee_bulk', [
                                        'years' => $years ?? [],
                                        'months' => $months ?? [],
                                        'classes' => $classes ?? [],
                                        'sections' => $sections ?? [],
                                        'accounts' => $accounts ?? [],
                                    ])
                                </div>

                            </div>



                            {{-- Donation --}}
                            <div x-show="tab==='donation'" x-cloak>
                                <form method="POST" action="{{ url('/transactions/quick-store') }}"
                                    class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="type_key" value="donation">

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Donor</label>
                                        <div class="flex items-center gap-2">
                                            <select id="select_donation_donor" data-entity-select="donors"
                                                name="doner_id"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                                <option value="">Select donor</option>
                                                @foreach ($donors ?? [] as $d)
                                                    @php
                                                        $label =
                                                            $d->name ??
                                                            ($d->donor_name ?? ($d->doner_name ?? 'Donor #' . $d->id));
                                                    @endphp
                                                    <option value="{{ $d->id }}" @selected($oldSelected('doner_id', $d->id, 'donation'))>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('donors', 'select_donation_donor')"
                                                class="shrink-0 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs hover:bg-slate-50">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Amount</label>
                                        <input type="number" step="0.01" name="amount"
                                            value="{{ $oldFor('amount', 'donation') }}"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        <p class="text-[11px] text-slate-500 mt-1">Will be saved as Debit (cash in).
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Account</label>
                                        <div class="flex items-center gap-2">
                                            {{-- ✅ FIX: id + data-entity-select --}}
                                            <select id="select_donation_account" data-entity-select="accounts"
                                                name="account_id"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                                <option value="">Select account</option>
                                                @foreach ($accounts ?? [] as $acc)
                                                    <option value="{{ $acc->id }}" @selected($oldSelected('account_id', $acc->id, 'donation'))>
                                                        {{ $acc->name ?? 'Account #' . $acc->id }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('accounts', 'select_donation_account')"
                                                class="shrink-0 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs hover:bg-slate-50">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Date</label>
                                            <input type="date" name="transactions_date"
                                                value="{{ $oldFor('transactions_date', 'donation') }}"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Receipt
                                                No</label>
                                            <input type="text" name="recipt_no"
                                                value="{{ $oldFor('recipt_no', 'donation') }}"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Note</label>
                                        <input type="text" name="note"
                                            value="{{ $oldFor('note', 'donation') }}"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                    </div>

                                    <button type="submit"
                                        class="w-full rounded-xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                                        Save Donation
                                    </button>
                                </form>
                            </div>

                            {{-- Loan Taken --}}
                            <div x-show="tab==='loan_taken'" x-cloak>
                                <form method="POST" action="{{ url('/transactions/quick-store') }}"
                                    class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="type_key" value="loan_taken">

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Lender</label>
                                        <div class="flex items-center gap-2">
                                            <select id="select_loan_taken_lender" data-entity-select="lenders"
                                                name="lender_id"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                                <option value="">Select lender</option>
                                                @foreach ($lenders ?? [] as $l)
                                                    @php $label = $l->name ?? ($l->lender_name ?? 'Lender #' . $l->id); @endphp
                                                    <option value="{{ $l->id }}" @selected($oldSelected('lender_id', $l->id, 'loan_taken'))>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('lenders', 'select_loan_taken_lender')"
                                                class="shrink-0 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs hover:bg-slate-50">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Amount</label>
                                        <input type="number" step="0.01" name="amount"
                                            value="{{ $oldFor('amount', 'loan_taken') }}"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        <p class="text-[11px] text-slate-500 mt-1">Loan taken = Debit (cash in).</p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Account</label>
                                        <div class="flex items-center gap-2">
                                            {{-- ✅ FIX: id + data-entity-select --}}
                                            <select id="select_loan_taken_account" data-entity-select="accounts"
                                                name="account_id"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                                <option value="">Select account</option>
                                                @foreach ($accounts ?? [] as $acc)
                                                    <option value="{{ $acc->id }}" @selected($oldSelected('account_id', $acc->id, 'loan_taken'))>
                                                        {{ $acc->name ?? 'Account #' . $acc->id }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('accounts', 'select_loan_taken_account')"
                                                class="shrink-0 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs hover:bg-slate-50">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Date</label>
                                            <input type="date" name="transactions_date"
                                                value="{{ $oldFor('transactions_date', 'loan_taken') }}"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Receipt
                                                No</label>
                                            <input type="text" name="recipt_no"
                                                value="{{ $oldFor('recipt_no', 'loan_taken') }}"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Note</label>
                                        <input type="text" name="note"
                                            value="{{ $oldFor('note', 'loan_taken') }}"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                    </div>

                                    <button type="submit"
                                        class="w-full rounded-xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                                        Save Loan
                                    </button>
                                </form>
                            </div>

                            {{-- Loan Repayment --}}
                            <div x-show="tab==='loan_repayment'" x-cloak>
                                <form method="POST" action="{{ url('/transactions/quick-store') }}"
                                    class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="type_key" value="loan_repayment">

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Lender</label>
                                        <div class="flex items-center gap-2">
                                            <select id="select_loan_repayment_lender" data-entity-select="lenders"
                                                name="lender_id"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                                <option value="">Select lender</option>
                                                @foreach ($lenders ?? [] as $l)
                                                    @php $label = $l->name ?? ($l->lender_name ?? 'Lender #' . $l->id); @endphp
                                                    <option value="{{ $l->id }}" @selected($oldSelected('lender_id', $l->id, 'loan_repayment'))>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('lenders', 'select_loan_repayment_lender')"
                                                class="shrink-0 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs hover:bg-slate-50">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Amount</label>
                                        <input type="number" step="0.01" name="amount"
                                            value="{{ $oldFor('amount', 'loan_repayment') }}"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        <p class="text-[11px] text-slate-500 mt-1">Repayment = Credit (cash out).</p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Account</label>
                                        <div class="flex items-center gap-2">
                                            {{-- ✅ FIX: id + data-entity-select --}}
                                            <select id="select_loan_repayment_account" data-entity-select="accounts"
                                                name="account_id"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                                <option value="">Select account</option>
                                                @foreach ($accounts ?? [] as $acc)
                                                    <option value="{{ $acc->id }}" @selected($oldSelected('account_id', $acc->id, 'loan_repayment'))>
                                                        {{ $acc->name ?? 'Account #' . $acc->id }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('accounts', 'select_loan_repayment_account')"
                                                class="shrink-0 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs hover:bg-slate-50">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Date</label>
                                            <input type="date" name="transactions_date"
                                                value="{{ $oldFor('transactions_date', 'loan_repayment') }}"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Receipt
                                                No</label>
                                            <input type="text" name="recipt_no"
                                                value="{{ $oldFor('recipt_no', 'loan_repayment') }}"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Note</label>
                                        <input type="text" name="note"
                                            value="{{ $oldFor('note', 'loan_repayment') }}"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                    </div>

                                    <button type="submit"
                                        class="w-full rounded-xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                                        Save Repayment
                                    </button>
                                </form>
                            </div>

                            {{-- Expense --}}
                            <div x-show="tab==='expense'" x-cloak>
                                <form method="POST" action="{{ url('/transactions/quick-store') }}"
                                    class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="type_key" value="expense">

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Expense
                                            Title</label>
                                        <input type="text" name="title"
                                            value="{{ $oldFor('title', 'expense') }}"
                                            placeholder="e.g., Electricity bill"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Amount</label>
                                        <input type="number" step="0.01" name="amount"
                                            value="{{ $oldFor('amount', 'expense') }}"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        <p class="text-[11px] text-slate-500 mt-1">Expense = Credit (cash out).</p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Account</label>
                                        <div class="flex items-center gap-2">
                                            {{-- ✅ FIX: id + data-entity-select --}}
                                            <select id="select_expense_account" data-entity-select="accounts"
                                                name="account_id"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                                <option value="">Select account</option>
                                                @foreach ($accounts ?? [] as $acc)
                                                    <option value="{{ $acc->id }}" @selected($oldSelected('account_id', $acc->id, 'expense'))>
                                                        {{ $acc->name ?? 'Account #' . $acc->id }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('accounts', 'select_expense_account')"
                                                class="shrink-0 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs hover:bg-slate-50">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Date</label>
                                            <input type="date" name="transactions_date"
                                                value="{{ $oldFor('transactions_date', 'expense') }}"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Receipt
                                                No</label>
                                            <input type="text" name="recipt_no"
                                                value="{{ $oldFor('recipt_no', 'expense') }}"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Note</label>
                                        <input type="text" name="note"
                                            value="{{ $oldFor('note', 'expense') }}"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                    </div>

                                    <button type="submit"
                                        class="w-full rounded-xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                                        Save Expense
                                    </button>
                                </form>
                            </div>

                            {{-- Income --}}
                            <div x-show="tab==='income'" x-cloak>
                                <form method="POST" action="{{ url('/transactions/quick-store') }}"
                                    class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="type_key" value="income">

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Income
                                            Title</label>
                                        <input type="text" name="title"
                                            value="{{ $oldFor('title', 'income') }}"
                                            placeholder="e.g., Service income"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Amount</label>
                                        <input type="number" step="0.01" name="amount"
                                            value="{{ $oldFor('amount', 'income') }}"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        <p class="text-[11px] text-slate-500 mt-1">Income = Debit (cash in).</p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Account</label>
                                        <div class="flex items-center gap-2">
                                            {{-- ✅ FIX: id + data-entity-select --}}
                                            <select id="select_income_account" data-entity-select="accounts"
                                                name="account_id"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                                <option value="">Select account</option>
                                                @foreach ($accounts ?? [] as $acc)
                                                    <option value="{{ $acc->id }}" @selected($oldSelected('account_id', $acc->id, 'income'))>
                                                        {{ $acc->name ?? 'Account #' . $acc->id }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('accounts', 'select_income_account')"
                                                class="shrink-0 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs hover:bg-slate-50">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Date</label>
                                            <input type="date" name="transactions_date"
                                                value="{{ $oldFor('transactions_date', 'income') }}"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Receipt
                                                No</label>
                                            <input type="text" name="recipt_no"
                                                value="{{ $oldFor('recipt_no', 'income') }}"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Note</label>
                                        <input type="text" name="note" value="{{ $oldFor('note', 'income') }}"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                    </div>

                                    <button type="submit"
                                        class="w-full rounded-xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                                        Save Income
                                    </button>
                                </form>
                            </div>

                        </div>

                        {{-- Modal (Phase 3) --}}
                        <div x-show="modalOpen" x-cloak
                            class="fixed inset-0 z-50 flex items-center justify-center p-4">
                            <div class="fixed inset-0 bg-slate-900/60" @click="closeModal()"></div>

                            <div
                                class="relative w-full max-w-md rounded-2xl bg-white border border-slate-200 shadow-xl overflow-hidden">
                                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-800" x-text="modalTitle"></div>
                                        <div class="text-xs text-slate-500">Create → auto select</div>
                                    </div>
                                    <button type="button" @click="closeModal()"
                                        class="rounded-lg border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">✕</button>
                                </div>

                                <div class="p-4 space-y-3">
                                    <template x-if="modalErrors.length">
                                        <div
                                            class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-rose-800 text-xs">
                                            <div class="font-semibold mb-1">Fix these:</div>
                                            <ul class="list-disc list-inside space-y-1">
                                                <template x-for="(e, idx) in modalErrors" :key="idx">
                                                    <li x-text="e"></li>
                                                </template>
                                            </ul>
                                        </div>
                                    </template>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Name</label>
                                        <input type="text" x-model="modalForm.name"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200"
                                            placeholder="Enter name">
                                    </div>

                                    {{-- Students extra --}}
                                    <div x-show="modalEntity==='students'" x-cloak class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Mobile</label>
                                            <input type="text" x-model="modalForm.mobile"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200"
                                                placeholder="01xxxxxxxxx">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Email</label>
                                            <input type="email" x-model="modalForm.email"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200"
                                                placeholder="email@example.com">
                                        </div>
                                    </div>

                                    {{-- Donors extra --}}
                                    <div x-show="modalEntity==='donors'" x-cloak class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Mobile</label>
                                            <input type="text" x-model="modalForm.mobile"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200"
                                                placeholder="01xxxxxxxxx">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Email</label>
                                            <input type="email" x-model="modalForm.email"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200"
                                                placeholder="email@example.com">
                                        </div>
                                    </div>

                                    {{-- Accounts extra --}}
                                    <div x-show="modalEntity==='accounts'" x-cloak>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Account
                                            Details</label>
                                        <input type="text" x-model="modalForm.account_details"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200"
                                            placeholder="e.g., Cash in hand">
                                        <div class="mt-2">
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Account Number
                                                (optional)</label>
                                            <input type="text" x-model="modalForm.account_number"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200"
                                                placeholder="Leave blank for auto">
                                        </div>
                                    </div>

                                    {{-- Lenders extra --}}
                                    <div x-show="modalEntity==='lenders'" x-cloak class="space-y-2">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 mb-1">Phone</label>
                                                <input type="text" x-model="modalForm.phone"
                                                    class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200"
                                                    placeholder="01xxxxxxxxx">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 mb-1">Email</label>
                                                <input type="email" x-model="modalForm.email"
                                                    class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200"
                                                    placeholder="email@example.com">
                                            </div>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 mb-1">Address</label>
                                            <input type="text" x-model="modalForm.address"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200"
                                                placeholder="Address">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Bank
                                                Details</label>
                                            <input type="text" x-model="modalForm.bank_detils"
                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200"
                                                placeholder="Bank details">
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-2 pt-2">
                                        <button type="button" @click="closeModal()"
                                            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">
                                            Cancel
                                        </button>
                                        <button type="button" @click="submitModal()" :disabled="saving"
                                            class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm hover:bg-slate-800 disabled:opacity-60">
                                            <span x-show="!saving">Create</span>
                                            <span x-show="saving" x-cloak>Saving...</span>
                                        </button>
                                    </div>

                                    <p class="text-[11px] text-slate-500">
                                        AJAX: <code
                                            class="px-1 py-0.5 bg-slate-50 border border-slate-200 rounded">POST
                                            /ajax/{entity}</code>
                                    </p>
                                </div>
                            </div>
                        </div>



                    </div>
                </aside>

            </div>
        </div>
    </div>

    {{-- Page JS (vanilla + Alpine) --}}

    {{-- modal --}}
    <script>
        function transactionCenterQuickAdd(initialTab = 'student_fee', initialFeeMode = 'single') {
            return {
                tab: initialTab,
                feeMode: initialFeeMode,
                tabClass: 'rounded-xl border border-slate-200 bg-white px-2 py-2 hover:bg-slate-50',
                activeTabClass: 'rounded-xl border border-slate-900 bg-slate-900 text-white px-2 py-2 hover:bg-slate-800',

                modalOpen: false,
                modalEntity: null,
                modalTitle: '',
                modalTargetSelectId: null,
                modalErrors: [],
                saving: false,

                modalForm: {
                    name: '',
                    mobile: '',
                    email: '',
                    phone: '',
                    address: '',
                    bank_detils: '',
                    account_number: '',
                    account_details: '',
                },

                openModal(entity, targetSelectId) {
                    this.modalEntity = entity;
                    this.modalTargetSelectId = targetSelectId || null;
                    this.modalTitle = 'Add ' + entity.replace('_', ' ').replace(/\b\w/g, m => m.toUpperCase());
                    this.modalErrors = [];
                    this.saving = false;

                    this.modalForm = {
                        name: '',
                        mobile: '',
                        email: '',
                        phone: '',
                        address: '',
                        bank_detils: '',
                        account_number: '',
                        account_details: '',
                    };

                    this.modalOpen = true;
                },

                closeModal() {
                    this.modalOpen = false;
                    this.modalEntity = null;
                    this.modalTargetSelectId = null;
                    this.modalErrors = [];
                    this.saving = false;
                },

                async submitModal() {
                    this.modalErrors = [];
                    this.saving = true;

                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                        '{{ csrf_token() }}';

                    let payload = {
                        name: this.modalForm.name
                    };

                    if (this.modalEntity === 'students' || this.modalEntity === 'donors') {
                        payload.mobile = this.modalForm.mobile;
                        payload.email = this.modalForm.email;
                    }

                    if (this.modalEntity === 'lenders') {
                        payload.phone = this.modalForm.phone;
                        payload.email = this.modalForm.email;
                        payload.address = this.modalForm.address;
                        payload.bank_detils = this.modalForm.bank_detils;
                    }

                    if (this.modalEntity === 'accounts') {
                        payload.account_number = this.modalForm.account_number;
                        payload.account_details = this.modalForm.account_details;
                    }

                    try {
                        const res = await fetch(`/ajax/${this.modalEntity}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await res.json().catch(() => ({}));

                        if (!res.ok) {
                            if (data?.errors) {
                                this.modalErrors = Object.values(data.errors).flat();
                            } else if (data?.message) {
                                this.modalErrors = [data.message];
                            } else {
                                this.modalErrors = ['Something went wrong'];
                            }
                            this.saving = false;
                            return;
                        }

                        // ✅ append option to ALL selects for this entity
                        const selects = document.querySelectorAll(`select[data-entity-select="${this.modalEntity}"]`);
                        selects.forEach(sel => {
                            const opt = document.createElement('option');
                            opt.value = data.id;
                            opt.textContent = data.name || (`#${data.id}`);
                            sel.appendChild(opt);
                        });

                        // ✅ auto-select target select if provided
                        if (this.modalTargetSelectId) {
                            const target = document.getElementById(this.modalTargetSelectId);
                            if (target) target.value = String(data.id);
                        }

                        this.closeModal();

                    } catch (e) {
                        this.modalErrors = ['Network error / server error'];
                        this.saving = false;
                    }
                },
            }
        }
    </script>


    {{-- date range buttons --}}
    <script>
        // ✅ Phase 2.6: Date range buttons (Today / This Month) -> auto submit
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('[data-filter-form]');
            if (!form) return;

            const from = form.querySelector('input[name="from"]');
            const to = form.querySelector('input[name="to"]');
            if (!from || !to) return;

            const pad = n => String(n).padStart(2, '0');
            const fmt = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

            document.querySelectorAll('[data-range]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const now = new Date();

                    if (btn.dataset.range === 'today') {
                        const ymd = fmt(now);
                        from.value = ymd;
                        to.value = ymd;
                    }

                    if (btn.dataset.range === 'month') {
                        const first = new Date(now.getFullYear(), now.getMonth(), 1);
                        const last = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                        from.value = fmt(first);
                        to.value = fmt(last);
                    }

                    form.submit();
                });
            });
        });

        // Alpine data
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>


</x-app-layout>
