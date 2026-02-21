{{-- resources/views/transactions/center.blade.php --}}
@php
    $pageTitle = 'Transaction Center';
    $today = now()->toDateString();

    // ✅ Consistent UI input styles (light/dark)
    $inputClass =
        'w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 text-sm text-slate-900 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500';
    $selectClass = $inputClass;
    $readonlyClass =
        'w-full rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-sm text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500';

    // ✅ Ledger-aware helpers (Phase 1/4): IN = credit, OUT = debit
    $getAmount = function ($tx) {
        $in = (float) ($tx->credit ?? 0); // IN
        $out = (float) ($tx->debit ?? 0); // OUT
        return $in > 0 ? $in : $out;
    };

    $getDirection = function ($tx) {
        $in = (float) ($tx->credit ?? 0);
        return $in > 0 ? 'IN' : 'OUT';
    };

    $getParty = function ($tx) {
        $studentName =
            data_get($tx, 'student.full_name') ??
            (data_get($tx, 'student.name') ?? data_get($tx, 'student.student_name'));

        if (!$studentName) {
            $fn = data_get($tx, 'student.first_name');
            $ln = data_get($tx, 'student.last_name');
            $studentName = trim(($fn ?? '') . ' ' . ($ln ?? '')) ?: null;
        }

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

    $getTitle = function ($tx) {
        // আপনার transactions টেবিলে যেই কলাম থাকতে পারে সেগুলো চেক
        $title = data_get($tx, 'title') ?? (data_get($tx, 'expense_title') ?? data_get($tx, 'income_title'));

        $title = trim((string) $title);
        return $title !== '' ? $title : null;
    };

    // ✅ Active tab + per-tab old helpers
    $activeTab = old('type_key', 'student_fee');

    $oldFor = function ($key, $tab, $default = '') use ($activeTab) {
        return $activeTab === $tab ? old($key, $default) : $default;
    };

    $oldSelected = function ($key, $value, $tab) use ($activeTab) {
        return $activeTab === $tab && (string) old($key) === (string) $value;
    };

    // ✅ Load missing dropdowns safely (for Expense/Income required fields)
    $loadSimpleTable = function (string $table) {
        $Schema = \Illuminate\Support\Facades\Schema::class;
        $DB = \Illuminate\Support\Facades\DB::class;

        if (!$Schema::hasTable($table)) {
            return collect();
        }

        $q = $DB::table($table);

        if ($Schema::hasColumn($table, 'isDeleted')) {
            $q->where('isDeleted', false);
        }
        if ($Schema::hasColumn($table, 'isActived')) {
            $q->where('isActived', true);
        }

        // select safe columns
        $cols = ['id'];
        foreach (['name', 'title'] as $c) {
            if ($Schema::hasColumn($table, $c)) {
                $cols[] = $c;
            }
        }
        $q->select($cols)->orderByDesc('id');

        return $q->get();
    };

    // tables: catagories, expens, incomes
    $catagories = $catagories ?? ($categories ?? $loadSimpleTable('catagories'));
    $expensList = $expensList ?? ($expenseHeads ?? $loadSimpleTable('expens'));
    $incomesList = $incomesList ?? ($incomeHeads ?? $loadSimpleTable('incomes'));

    // ✅ Endpoints
    $studentQuickEndpoint = \Illuminate\Support\Facades\Route::has('transaction-center.students.quick')
        ? route('transaction-center.students.quick')
        : url('/transaction-center/students/quick');

    $classDefaultFeesEndpoint =
        $classDefaultFeesEndpoint ??
        (Route::has('ajax.class_default_fees') ? route('ajax.class_default_fees') : url('/ajax/class-default-fees'));
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">{{ $pageTitle }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">All transactions in one place — filter + review + quick add</p>
            </div>

            {{-- <div class="flex items-center gap-2 flex-wrap justify-end">
                <a href="{{ url('/dashboard') }}"
                    class="inline-flex items-center rounded-xl bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-700">
                    Dashboard
                </a>

                <a href="{{ url('/chart-of-accounts') }}"
                    class="inline-flex items-center rounded-xl bg-green-600 px-3 py-2 text-sm text-white hover:bg-green-700">
                    Chart of Accounts
                </a>

                <a href="/settings"
                    class="inline-flex items-center rounded-xl bg-orange-600 px-3 py-2 text-sm text-white hover:bg-orange-700">
                    Academic Calendar
                </a>

                <a href="{{ url('/list-student-fees') }}"
                    class="inline-flex items-center rounded-xl bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-700">
                    Fees List
                </a>

                <a href="{{ route('boarding.students.index') }}"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm hover:bg-slate-50">
                    Boarding Students
                </a>
            </div> --}}
        </div>
    </x-slot>

    <div class="py-6" x-data="transactionCenterPage()" x-init="init()">
        {{-- Toast --}}
        <div x-show="toast.show" x-cloak class="fixed z-50 right-4 bottom-4 max-w-md w-[calc(100vw-2rem)] sm:w-auto">
            <div class="rounded-2xl shadow-xl border px-4 py-3 text-sm"
                :class="toast.type === 'success' ?
                    'bg-emerald-50 border-emerald-200 text-emerald-800' :
                    'bg-rose-50 border-rose-200 text-rose-800'">
                <div class="flex items-start gap-3">
                    <div class="font-semibold" x-text="toast.type==='success' ? 'Success' : 'Error'"></div>
                    <div class="flex-1" x-text="toast.message"></div>
                    <button type="button" class="text-xs opacity-70 hover:opacity-100"
                        @click="toast.show=false">✕</button>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-12 gap-4">

                {{-- MAIN --}}
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

                    {{-- Filters --}}
                    <form method="GET" action="{{ url('/transaction-center') }}" data-filter-form
                        class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm p-4">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Filters</h3>

                                <div class="flex items-center gap-2">
                                    <button type="button" data-range="today"
                                        class="text-xs rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-2 py-1 hover:bg-slate-50 dark:hover:bg-white/5">
                                        Today
                                    </button>
                                    <button type="button" data-range="month"
                                        class="text-xs rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-2 py-1 hover:bg-slate-50 dark:hover:bg-white/5">
                                        This Month
                                    </button>

                                    <a href="{{ url('/transaction-center') }}"
                                        class="text-xs rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-2 py-1 hover:bg-slate-50 dark:hover:bg-white/5">
                                        Reset
                                    </a>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-3">
                                <div class="col-span-12 sm:col-span-3">
                                    <label
                                        class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">From</label>
                                    <input type="date" name="from" value="{{ request('from') }}"
                                        class="{{ $inputClass }}">
                                </div>

                                <div class="col-span-12 sm:col-span-3">
                                    <label
                                        class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">To</label>
                                    <input type="date" name="to" value="{{ request('to') }}"
                                        class="{{ $inputClass }}">
                                </div>

                                <div class="col-span-12 sm:col-span-3">
                                    <label
                                        class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Account</label>
                                    <select name="account_id" class="{{ $selectClass }}">
                                        <option value="">All accounts</option>
                                        @foreach ($accounts ?? [] as $acc)
                                            <option value="{{ $acc->id }}" @selected((string) $acc->id === (string) request('account_id'))>
                                                {{ $acc->name ?? 'Account #' . $acc->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-12 sm:col-span-3">
                                    <label
                                        class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Type</label>
                                    <select name="type_id" class="{{ $selectClass }}">
                                        <option value="">All types</option>
                                        @foreach ($types ?? [] as $t)
                                            <option value="{{ $t->id }}" @selected((string) $t->id === (string) request('type_id'))>
                                                {{ $t->name ?? 'Type #' . $t->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-12 sm:col-span-9">
                                    <label
                                        class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Search</label>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Receipt no / note / keyword…" class="{{ $inputClass }}">
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
                    <div
                        class="mt-4 bg-white dark:bg-slate-950 border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden">
                        <div
                            class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-white/10">
                            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Transactions</h3>
                            <div class="text-xs text-slate-500">
                                Showing
                                {{ method_exists($transactions ?? null, 'total') ? $transactions->total() : (is_countable($transactions ?? []) ? count($transactions) : 0) }}
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead
                                    class="bg-slate-50 dark:bg-white/5 text-slate-600 dark:text-slate-300 text-xs uppercase">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Date</th>
                                        <th class="px-4 py-3 text-left">Type</th>
                                        <th class="px-4 py-3 text-left">Party / Title</th>
                                        <th class="px-4 py-3 text-left">Account</th>
                                        <th class="px-4 py-3 text-right">Amount</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                                    @forelse(($transactions ?? []) as $tx)
                                        @php
                                            $party = $getParty($tx);
                                            $title = $getTitle($tx);
                                            $amount = $getAmount($tx);
                                            $dir = $getDirection($tx);

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

                                            $isIn = ((float) ($tx->credit ?? 0)) > 0; // ✅ IN = credit
                                        @endphp

                                        <tr class="hover:bg-slate-50/70 dark:hover:bg-white/5">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="font-medium text-slate-800 dark:text-slate-100">
                                                    {{ $tx->transactions_date ?? '-' }}
                                                </div>
                                                <div class="text-xs text-slate-500">#{{ $tx->id }}</div>
                                            </td>

                                            <td class="px-4 py-3">
                                                <span
                                                    class="inline-flex items-center rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-2 py-1 text-xs text-slate-700 dark:text-slate-200">
                                                    {{ $typeName }}
                                                </span>
                                            </td>

                                            <td class="px-4 py-3">
                                                @php
                                                    $hasParty =
                                                        ($party['label'] ?? '-') !== '-' &&
                                                        ($party['name'] ?? '-') !== '-';
                                                    $hasTitle = !empty($title);
                                                @endphp

                                                {{-- ✅ Main line --}}
                                                <div class="font-medium text-slate-800 dark:text-slate-100">
                                                    @if ($hasParty)
                                                        {{ $party['name'] }}
                                                    @elseif($hasTitle)
                                                        {{ $title }}
                                                    @else
                                                        -
                                                    @endif
                                                </div>

                                                {{-- ✅ Sub lines --}}
                                                @if ($hasParty)
                                                    <div class="text-xs text-slate-500">{{ $party['label'] }}</div>
                                                @endif

                                                @if ($hasTitle && $hasParty)
                                                    <div class="text-xs text-slate-500">Title: {{ $title }}
                                                    </div>
                                                @elseif($hasTitle && !$hasParty)
                                                    <div class="text-xs text-slate-500">Title</div>
                                                @endif
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="text-slate-800 dark:text-slate-100">{{ $accountName }}
                                                </div>
                                                <div class="text-xs text-slate-500">
                                                    {{ $tx->recipt_no ? 'Receipt: ' . $tx->recipt_no : ($tx->student_book_number ? 'Book: ' . $tx->student_book_number : '') }}
                                                </div>
                                            </td>

                                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                                <div
                                                    class="font-semibold {{ $isIn ? 'text-emerald-700' : 'text-rose-700' }}">
                                                    {{ number_format((float) $amount, 2) }}
                                                </div>
                                                <div class="text-xs text-slate-500">{{ $dir }}</div>
                                            </td>

                                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center gap-2">
                                                    @if (\Illuminate\Support\Facades\Route::has('transactions.receipt'))
                                                        <a href="{{ route('transactions.receipt', $tx->id) }}"
                                                            target="_blank"
                                                            class="text-xs rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-2 py-1 hover:bg-slate-50 dark:hover:bg-white/5">
                                                            Print
                                                        </a>
                                                    @else
                                                        <button type="button" disabled
                                                            class="text-xs rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-2 py-1 opacity-50 cursor-not-allowed"
                                                            title="Receipt/Print route missing">
                                                            Print
                                                        </button>
                                                    @endif

                                                    @if (\Illuminate\Support\Facades\Route::has('transactions.edit'))
                                                        <a href="{{ route('transactions.edit', $tx->id) }}"
                                                            class="text-xs rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-2 py-1 hover:bg-slate-50 dark:hover:bg-white/5">
                                                            Edit
                                                        </a>
                                                    @else
                                                        <button type="button" disabled
                                                            class="text-xs rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-2 py-1 opacity-50 cursor-not-allowed"
                                                            title="Edit route missing">
                                                            Edit
                                                        </button>
                                                    @endif

                                                    @if (\Illuminate\Support\Facades\Route::has('transactions.destroy'))
                                                        <form method="POST"
                                                            action="{{ route('transactions.destroy', $tx->id) }}"
                                                            onsubmit="return confirm('Are you sure you want to delete this transaction?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="text-xs rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-rose-700 hover:bg-rose-100">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button type="button" disabled
                                                            class="text-xs rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-2 py-1 opacity-50 cursor-not-allowed"
                                                            title="Delete route missing">
                                                            Delete
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
                            <div class="px-4 py-3 border-t border-slate-200 dark:border-white/10">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    </div>
                </section>

                {{-- RIGHT PANEL --}}
                <aside class="col-span-12 lg:col-span-4">
                    @php
                        $activeTab = old('type_key', 'student_fee');
                        $feeMode = old('_tc_fee_mode', 'single');
                    @endphp

                    <div x-data="transactionCenterQuickAdd(@js($activeTab), @js($feeMode))"
                        class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200 dark:border-white/10">
                            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Quick Add</h3>
                            <p class="text-xs text-slate-500 mt-1">Add transactions without leaving this page</p>
                        </div>

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

                        {{-- ✅ Card Selector (Phase 4) --}}
                        <div class="p-4">
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="tab='student_fee'"
                                    class="rounded-2xl border px-3 py-3 text-left dark:hover:bg-white/5"
                                    :class="tab === 'student_fee' ?
                                        'bg-slate-900 text-white border-slate-900' :
                                        'bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 text-slate-800 dark:text-slate-100'">
                                    <div class="text-xs opacity-80">IN</div>
                                    <div class="text-sm font-semibold">Student Fee</div>
                                    <div class="text-[11px] opacity-75">Credit</div>
                                </button>

                                <button type="button" @click="tab='expense'"
                                    class="rounded-2xl border px-3 py-3 text-left dark:hover:bg-white/5"
                                    :class="tab === 'expense' ?
                                        'bg-slate-900 text-white border-slate-900' :
                                        'bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 text-slate-800 dark:text-slate-100'">
                                    <div class="text-xs opacity-80">OUT</div>
                                    <div class="text-sm font-semibold">Expense</div>
                                    <div class="text-[11px] opacity-75">Debit</div>
                                </button>

                                <button type="button" @click="tab='donation'"
                                    class="rounded-2xl border px-3 py-3 text-left dark:hover:bg-white/5"
                                    :class="tab === 'donation' ?
                                        'bg-slate-900 text-white border-slate-900' :
                                        'bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 text-slate-800 dark:text-slate-100'">
                                    <div class="text-xs opacity-80">IN</div>
                                    <div class="text-sm font-semibold">Donation</div>
                                    <div class="text-[11px] opacity-75">Credit</div>
                                </button>
                            </div>

                            <div class="grid grid-cols-3 gap-2 mt-2 text-xs">
                                <button type="button" @click="tab='loan_taken'"
                                    :class="tab === 'loan_taken' ? activeTabClass : tabClass">Loan</button>
                                <button type="button" @click="tab='loan_repayment'"
                                    :class="tab === 'loan_repayment' ? activeTabClass : tabClass">Repayment</button>
                                <button type="button" @click="tab='income'"
                                    :class="tab === 'income' ? activeTabClass : tabClass">Income</button>
                            </div>
                        </div>

                        {{-- Forms --}}
                        <div class="px-4 pb-4 space-y-4">

                            {{-- Student Fee --}}
                            <div x-show="tab==='student_fee'" x-cloak class="space-y-3">

                                {{-- Sub Tabs --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" @click="feeMode='single'"
                                        class="rounded-xl border px-3 py-2 text-xs"
                                        :class="feeMode === 'single'
                                            ?
                                            'bg-slate-900 text-white border-slate-900' :
                                            'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/5'">
                                        Single Student
                                    </button>

                                    <button type="button" @click="feeMode='bulk'"
                                        class="rounded-xl border px-3 py-2 text-xs"
                                        :class="feeMode === 'bulk'
                                            ?
                                            'bg-slate-900 text-white border-slate-900' :
                                            'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/5'">
                                        Bulk Students
                                    </button>
                                </div>

                                {{-- SINGLE --}}
                                <div x-show="feeMode==='single'" x-cloak
                                    class="rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 p-3">
                                    <div class="mb-2">
                                        <div class="text-sm font-semibold text-slate-800 dark:text-slate-100">Single
                                            Fee</div>
                                        <div class="text-xs text-slate-500">One student fee entry</div>
                                    </div>

                                    <form id="tc_single_fee_form" method="POST"
                                        action="{{ url('/transactions/quick-store') }}" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="type_key" value="student_fee">
                                        <input type="hidden" name="_tc_fee_mode" value="single">

                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Student</label>
                                            <div class="flex items-center gap-2">
                                                <select id="select_student_fee_student" data-entity-select="students"
                                                    name="student_id" class="{{ $selectClass }}">
                                                    <option value="">Select student</option>
                                                    @foreach ($students ?? [] as $s)
                                                        @php
                                                            $label =
                                                                $s->full_name ?? ($s->name ?? 'Student #' . $s->id);
                                                            $label = $s->roll
                                                                ? $label . ' (Roll: ' . $s->roll . ')'
                                                                : $label;
                                                        @endphp
                                                        <option value="{{ $s->id }}"
                                                            data-roll="{{ $s->roll }}"
                                                            data-class-id="{{ $s->class_id }}"
                                                            data-section-id="{{ $s->section_id }}"
                                                            data-academic-year-id="{{ $s->academic_year_id }}"
                                                            data-fees-type-id="{{ $s->fees_type_id }}"
                                                            @selected($oldSelected('student_id', $s->id, 'student_fee'))>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <button type="button"
                                                    @click="openModal('students', 'select_student_fee_student')"
                                                    class="shrink-0 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-white/5">
                                                    + Add
                                                </button>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Academic
                                                    Year</label>
                                                <select id="select_student_fee_year" name="academic_year_id"
                                                    class="{{ $selectClass }}">
                                                    <option value="">Select year</option>
                                                    @foreach ($years ?? [] as $y)
                                                        <option value="{{ $y->id }}"
                                                            @selected($oldSelected('academic_year_id', $y->id, 'student_fee'))>
                                                            {{ $y->name ?? 'Year #' . $y->id }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Month
                                                    <span class="text-rose-600">*</span></label>
                                                <select id="select_student_fee_month" name="months_id"
                                                    class="{{ $selectClass }}">
                                                    <option value="">Select month</option>
                                                    @foreach ($months ?? [] as $m)
                                                        <option value="{{ $m->id }}"
                                                            @selected($oldSelected('months_id', $m->id, 'student_fee'))>
                                                            {{ $m->name ?? 'Month #' . $m->id }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <div class="flex items-center justify-between mb-1">
                                                    <label
                                                        class="block text-xs font-medium text-slate-600 dark:text-slate-300">Class</label>

                                                    <button type="button" id="tc_apply_class_defaults"
                                                        class="text-[11px] rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-2 py-1 hover:bg-slate-50 dark:hover:bg-white/5">
                                                        Apply Defaults
                                                    </button>
                                                </div>

                                                <select id="select_student_fee_class" name="class_id"
                                                    class="{{ $selectClass }}">
                                                    <option value="">Select class</option>
                                                    @foreach ($classes ?? [] as $c)
                                                        <option value="{{ $c->id }}"
                                                            @selected($oldSelected('class_id', $c->id, 'student_fee'))>
                                                            {{ $c->name ?? 'Class #' . $c->id }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <p id="tc_defaults_status" class="text-[11px] text-slate-500 mt-1">
                                                </p>
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Section</label>
                                                <select id="select_student_fee_section" name="section_id"
                                                    class="{{ $selectClass }}">
                                                    <option value="">Select section</option>
                                                    @foreach ($sections ?? [] as $sec)
                                                        <option value="{{ $sec->id }}"
                                                            @selected($oldSelected('section_id', $sec->id, 'student_fee'))>
                                                            {{ $sec->name ?? 'Section #' . $sec->id }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Fees
                                                    Type</label>
                                                <select id="select_student_fee_fess_type" name="fess_type_id"
                                                    class="{{ $selectClass }}">
                                                    <option value="">Select fees type</option>
                                                    @foreach ($feesTypes ?? [] as $ft)
                                                        <option value="{{ $ft->id }}"
                                                            @selected($oldSelected('fess_type_id', $ft->id, 'student_fee'))>
                                                            {{ $ft->name ?? 'Type #' . $ft->id }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Roll
                                                    (Auto)</label>
                                                <input id="student_fee_roll" type="text" readonly
                                                    class="{{ $readonlyClass }}" placeholder="Auto from student">
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Student
                                                Book Number</label>
                                            <input type="text" name="student_book_number"
                                                value="{{ $oldFor('student_book_number', 'student_fee') }}"
                                                class="{{ $inputClass }}">
                                        </div>

                                        {{-- Fees breakdown --}}
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Admission
                                                    Fee</label>
                                                <input type="number" step="0.01" min="0"
                                                    name="admission_fee"
                                                    value="{{ $oldFor('admission_fee', 'student_fee') }}"
                                                    class="tc-fee-input {{ $inputClass }}">
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Monthly
                                                    Fee</label>
                                                <input type="number" step="0.01" min="0"
                                                    name="monthly_fees"
                                                    value="{{ $oldFor('monthly_fees', 'student_fee') }}"
                                                    class="tc-fee-input {{ $inputClass }}">
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Boarding
                                                    Fee</label>
                                                <input type="number" step="0.01" min="0"
                                                    name="boarding_fees"
                                                    value="{{ $oldFor('boarding_fees', 'student_fee') }}"
                                                    class="tc-fee-input {{ $inputClass }}">
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Management
                                                    Fee</label>
                                                <input type="number" step="0.01" min="0"
                                                    name="management_fees"
                                                    value="{{ $oldFor('management_fees', 'student_fee') }}"
                                                    class="tc-fee-input {{ $inputClass }}">
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Exam
                                                    Fee</label>
                                                <input type="number" step="0.01" min="0" name="exam_fees"
                                                    value="{{ $oldFor('exam_fees', 'student_fee') }}"
                                                    class="tc-fee-input {{ $inputClass }}">
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Others</label>
                                                <input type="number" step="0.01" min="0"
                                                    name="others_fees"
                                                    value="{{ $oldFor('others_fees', 'student_fee') }}"
                                                    class="tc-fee-input {{ $inputClass }}">
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Total
                                                Fee (Auto)</label>
                                            <input id="tc_single_total" type="number" step="0.01" min="0"
                                                name="total_fees" value="{{ $oldFor('total_fees', 'student_fee') }}"
                                                class="{{ $readonlyClass }}" readonly>
                                            <p class="text-[11px] text-slate-500 mt-1">
                                                Auto calculated. Ledger: Student Fee = <b>IN (credit)</b>.
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Account
                                                <span class="text-rose-600">*</span></label>
                                            <div class="flex items-center gap-2">
                                                <select id="select_student_fee_account" data-entity-select="accounts"
                                                    name="account_id" class="{{ $selectClass }}">
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
                                                    class="shrink-0 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-white/5">
                                                    + Add
                                                </button>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Date
                                                    <span class="text-rose-600">*</span></label>
                                                <input type="date" name="transactions_date"
                                                    value="{{ $oldFor('transactions_date', 'student_fee') ?: $today }}"
                                                    class="{{ $inputClass }}">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Receipt
                                                    No</label>
                                                <input type="text" name="recipt_no"
                                                    value="{{ $oldFor('recipt_no', 'student_fee') }}"
                                                    class="{{ $inputClass }}">
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Note</label>
                                            <input type="text" name="note"
                                                value="{{ $oldFor('note', 'student_fee') }}"
                                                class="{{ $inputClass }}">
                                        </div>

                                        <button type="submit"
                                            class="w-full rounded-xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                                            Save Fee
                                        </button>
                                    </form>
                                </div>

                                {{-- BULK --}}
                                <div x-show="feeMode==='bulk'" x-cloak
                                    class="rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 p-3">
                                    <div class="mb-2">
                                        <div class="text-sm font-semibold text-slate-800 dark:text-slate-100">Bulk Fees
                                        </div>
                                        <div class="text-xs text-slate-500">Class/Section/Month wise bulk entry</div>
                                    </div>

                                    @if (view()->exists('transactions.partials.forms.student_fee_bulk'))
                                        @include('transactions.partials.forms.student_fee_bulk', [
                                            'years' => $years ?? [],
                                            'months' => $months ?? [],
                                            'classes' => $classes ?? [],
                                            'sections' => $sections ?? [],
                                            'accounts' => $accounts ?? [],
                                        ])
                                    @else
                                        <div class="text-xs text-slate-500">
                                            Bulk form not added yet (transactions.partials.forms.student_fee_bulk).
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Donation --}}
                            <div x-show="tab==='donation'" x-cloak>
                                <form method="POST" action="{{ url('/transactions/quick-store') }}"
                                    class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="type_key" value="donation">

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Donor</label>
                                        <div class="flex items-center gap-2">
                                            <select id="select_donation_donor" data-entity-select="donors"
                                                name="doner_id" class="{{ $selectClass }}">
                                                <option value="">Select donor</option>
                                                @foreach ($donors ?? [] as $d)
                                                    @php $label = $d->name ?? ($d->donor_name ?? ($d->doner_name ?? 'Donor #' . $d->id)); @endphp
                                                    <option value="{{ $d->id }}" @selected($oldSelected('doner_id', $d->id, 'donation'))>
                                                        {{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('donors', 'select_donation_donor')"
                                                class="shrink-0 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-white/5">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Amount</label>
                                        <input type="number" step="0.01" name="amount"
                                            value="{{ $oldFor('amount', 'donation') }}" class="{{ $inputClass }}">
                                        <p class="text-[11px] text-slate-500 mt-1">Ledger: Donation = <b>IN
                                                (credit)</b>.</p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Account
                                            <span class="text-rose-600">*</span></label>
                                        <div class="flex items-center gap-2">
                                            <select id="select_donation_account" data-entity-select="accounts"
                                                name="account_id" class="{{ $selectClass }}">
                                                <option value="">Select account</option>
                                                @foreach ($accounts ?? [] as $acc)
                                                    <option value="{{ $acc->id }}" @selected($oldSelected('account_id', $acc->id, 'donation'))>
                                                        {{ $acc->name ?? 'Account #' . $acc->id }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('accounts', 'select_donation_account')"
                                                class="shrink-0 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-white/5">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Date
                                                <span class="text-rose-600">*</span></label>
                                            <input type="date" name="transactions_date"
                                                value="{{ $oldFor('transactions_date', 'donation') ?: $today }}"
                                                class="{{ $inputClass }}">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Receipt
                                                No</label>
                                            <input type="text" name="recipt_no"
                                                value="{{ $oldFor('recipt_no', 'donation') }}"
                                                class="{{ $inputClass }}">
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Note</label>
                                        <input type="text" name="note"
                                            value="{{ $oldFor('note', 'donation') }}" class="{{ $inputClass }}">
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
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Lender</label>
                                        <div class="flex items-center gap-2">
                                            <select id="select_loan_taken_lender" data-entity-select="lenders"
                                                name="lender_id" class="{{ $selectClass }}">
                                                <option value="">Select lender</option>
                                                @foreach ($lenders ?? [] as $l)
                                                    @php $label = $l->name ?? ($l->lender_name ?? 'Lender #' . $l->id); @endphp
                                                    <option value="{{ $l->id }}"
                                                        @selected($oldSelected('lender_id', $l->id, 'loan_taken'))>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('lenders', 'select_loan_taken_lender')"
                                                class="shrink-0 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-white/5">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Amount</label>
                                        <input type="number" step="0.01" name="amount"
                                            value="{{ $oldFor('amount', 'loan_taken') }}"
                                            class="{{ $inputClass }}">
                                        <p class="text-[11px] text-slate-500 mt-1">Ledger: Loan Taken = <b>IN
                                                (credit)</b>.</p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Account
                                            <span class="text-rose-600">*</span></label>
                                        <div class="flex items-center gap-2">
                                            <select id="select_loan_taken_account" data-entity-select="accounts"
                                                name="account_id" class="{{ $selectClass }}">
                                                <option value="">Select account</option>
                                                @foreach ($accounts ?? [] as $acc)
                                                    <option value="{{ $acc->id }}"
                                                        @selected($oldSelected('account_id', $acc->id, 'loan_taken'))>
                                                        {{ $acc->name ?? 'Account #' . $acc->id }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('accounts', 'select_loan_taken_account')"
                                                class="shrink-0 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-white/5">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Date
                                                <span class="text-rose-600">*</span></label>
                                            <input type="date" name="transactions_date"
                                                value="{{ $oldFor('transactions_date', 'loan_taken') ?: $today }}"
                                                class="{{ $inputClass }}">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Receipt
                                                No</label>
                                            <input type="text" name="recipt_no"
                                                value="{{ $oldFor('recipt_no', 'loan_taken') }}"
                                                class="{{ $inputClass }}">
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Note</label>
                                        <input type="text" name="note"
                                            value="{{ $oldFor('note', 'loan_taken') }}"
                                            class="{{ $inputClass }}">
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
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Lender</label>
                                        <div class="flex items-center gap-2">
                                            <select id="select_loan_repayment_lender" data-entity-select="lenders"
                                                name="lender_id" class="{{ $selectClass }}">
                                                <option value="">Select lender</option>
                                                @foreach ($lenders ?? [] as $l)
                                                    @php $label = $l->name ?? ($l->lender_name ?? 'Lender #' . $l->id); @endphp
                                                    <option value="{{ $l->id }}"
                                                        @selected($oldSelected('lender_id', $l->id, 'loan_repayment'))>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('lenders', 'select_loan_repayment_lender')"
                                                class="shrink-0 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-white/5">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Amount</label>
                                        <input type="number" step="0.01" name="amount"
                                            value="{{ $oldFor('amount', 'loan_repayment') }}"
                                            class="{{ $inputClass }}">
                                        <p class="text-[11px] text-slate-500 mt-1">Ledger: Repayment = <b>OUT
                                                (debit)</b>.</p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Account
                                            <span class="text-rose-600">*</span></label>
                                        <div class="flex items-center gap-2">
                                            <select id="select_loan_repayment_account" data-entity-select="accounts"
                                                name="account_id" class="{{ $selectClass }}">
                                                <option value="">Select account</option>
                                                @foreach ($accounts ?? [] as $acc)
                                                    <option value="{{ $acc->id }}"
                                                        @selected($oldSelected('account_id', $acc->id, 'loan_repayment'))>
                                                        {{ $acc->name ?? 'Account #' . $acc->id }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('accounts', 'select_loan_repayment_account')"
                                                class="shrink-0 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-white/5">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Date
                                                <span class="text-rose-600">*</span></label>
                                            <input type="date" name="transactions_date"
                                                value="{{ $oldFor('transactions_date', 'loan_repayment') ?: $today }}"
                                                class="{{ $inputClass }}">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Receipt
                                                No</label>
                                            <input type="text" name="recipt_no"
                                                value="{{ $oldFor('recipt_no', 'loan_repayment') }}"
                                                class="{{ $inputClass }}">
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Note</label>
                                        <input type="text" name="note"
                                            value="{{ $oldFor('note', 'loan_repayment') }}"
                                            class="{{ $inputClass }}">
                                    </div>

                                    <button type="submit"
                                        class="w-full rounded-xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                                        Save Repayment
                                    </button>
                                </form>
                            </div>

                            {{-- Expense (✅ catagory_id + expens_id REQUIRED) --}}
                            <div x-show="tab==='expense'" x-cloak>
                                <form method="POST" action="{{ url('/transactions/quick-store') }}"
                                    class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="type_key" value="expense">

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Expense
                                            Title</label>
                                        <input type="text" name="title"
                                            value="{{ $oldFor('title', 'expense') }}"
                                            placeholder="e.g., Electricity bill" class="{{ $inputClass }}">
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Category
                                                <span class="text-rose-600">*</span></label>
                                            <div class="flex items-center gap-2">
                                                <select id="select_expense_category" data-entity-select="catagories"
                                                    name="catagory_id" class="{{ $selectClass }}">
                                                    <option value="">Select</option>
                                                    @foreach ($catagories ?? [] as $c)
                                                        @php $nm = $c->name ?? ($c->title ?? ('Category #'.$c->id)); @endphp
                                                        <option value="{{ $c->id }}"
                                                            @selected($oldSelected('catagory_id', $c->id, 'expense'))>{{ $nm }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button"
                                                    @click="openModal('catagories', 'select_expense_category')"
                                                    class="shrink-0 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-white/5">
                                                    + Add
                                                </button>
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Expense
                                                Head <span class="text-rose-600">*</span></label>
                                            <div class="flex items-center gap-2">
                                                <select id="select_expense_head" data-entity-select="expens"
                                                    name="expens_id" class="{{ $selectClass }}">
                                                    <option value="">Select</option>
                                                    @foreach ($expensList ?? [] as $e)
                                                        @php $nm = $e->name ?? ($e->title ?? ('Head #'.$e->id)); @endphp
                                                        <option value="{{ $e->id }}"
                                                            @selected($oldSelected('expens_id', $e->id, 'expense'))>{{ $nm }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button"
                                                    @click="openModal('expens', 'select_expense_head')"
                                                    class="shrink-0 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-white/5">
                                                    + Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Amount</label>
                                        <input type="number" step="0.01" name="amount"
                                            value="{{ $oldFor('amount', 'expense') }}"
                                            class="{{ $inputClass }}">
                                        <p class="text-[11px] text-slate-500 mt-1">Ledger: Expense = <b>OUT
                                                (debit)</b>.</p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Account
                                            <span class="text-rose-600">*</span></label>
                                        <div class="flex items-center gap-2">
                                            <select id="select_expense_account" data-entity-select="accounts"
                                                name="account_id" class="{{ $selectClass }}">
                                                <option value="">Select account</option>
                                                @foreach ($accounts ?? [] as $acc)
                                                    <option value="{{ $acc->id }}"
                                                        @selected($oldSelected('account_id', $acc->id, 'expense'))>
                                                        {{ $acc->name ?? 'Account #' . $acc->id }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('accounts', 'select_expense_account')"
                                                class="shrink-0 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-white/5">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Date
                                                <span class="text-rose-600">*</span></label>
                                            <input type="date" name="transactions_date"
                                                value="{{ $oldFor('transactions_date', 'expense') ?: $today }}"
                                                class="{{ $inputClass }}">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Receipt
                                                No</label>
                                            <input type="text" name="recipt_no"
                                                value="{{ $oldFor('recipt_no', 'expense') }}"
                                                class="{{ $inputClass }}">
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Note</label>
                                        <input type="text" name="note"
                                            value="{{ $oldFor('note', 'expense') }}" class="{{ $inputClass }}">
                                    </div>

                                    <button type="submit"
                                        class="w-full rounded-xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                                        Save Expense
                                    </button>
                                </form>
                            </div>

                            {{-- Income (✅ income_id REQUIRED) --}}
                            <div x-show="tab==='income'" x-cloak>
                                <form method="POST" action="{{ url('/transactions/quick-store') }}"
                                    class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="type_key" value="income">

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Income
                                            Title</label>
                                        <input type="text" name="title"
                                            value="{{ $oldFor('title', 'income') }}"
                                            placeholder="e.g., Service income" class="{{ $inputClass }}">
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Income
                                                Head <span class="text-rose-600">*</span></label>
                                            <div class="flex items-center gap-2">
                                                <select id="select_income_head" data-entity-select="incomes"
                                                    name="income_id" class="{{ $selectClass }}">
                                                    <option value="">Select</option>
                                                    @foreach ($incomesList ?? [] as $i)
                                                        @php $nm = $i->name ?? ($i->title ?? ('Income #'.$i->id)); @endphp
                                                        <option value="{{ $i->id }}"
                                                            @selected($oldSelected('income_id', $i->id, 'income'))>{{ $nm }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button"
                                                    @click="openModal('incomes', 'select_income_head')"
                                                    class="shrink-0 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-white/5">
                                                    + Add
                                                </button>
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Category
                                                (optional)</label>
                                            <div class="flex items-center gap-2">
                                                <select id="select_income_category" data-entity-select="catagories"
                                                    name="catagory_id" class="{{ $selectClass }}">
                                                    <option value="">Select</option>
                                                    @foreach ($catagories ?? [] as $c)
                                                        @php $nm = $c->name ?? ($c->title ?? ('Category #'.$c->id)); @endphp
                                                        <option value="{{ $c->id }}"
                                                            @selected($oldSelected('catagory_id', $c->id, 'income'))>{{ $nm }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button"
                                                    @click="openModal('catagories', 'select_income_category')"
                                                    class="shrink-0 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-white/5">
                                                    + Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Amount</label>
                                        <input type="number" step="0.01" name="amount"
                                            value="{{ $oldFor('amount', 'income') }}" class="{{ $inputClass }}">
                                        <p class="text-[11px] text-slate-500 mt-1">Ledger: Income = <b>IN (credit)</b>.
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Account
                                            <span class="text-rose-600">*</span></label>
                                        <div class="flex items-center gap-2">
                                            <select id="select_income_account" data-entity-select="accounts"
                                                name="account_id" class="{{ $selectClass }}">
                                                <option value="">Select account</option>
                                                @foreach ($accounts ?? [] as $acc)
                                                    <option value="{{ $acc->id }}"
                                                        @selected($oldSelected('account_id', $acc->id, 'income'))>
                                                        {{ $acc->name ?? 'Account #' . $acc->id }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button"
                                                @click="openModal('accounts', 'select_income_account')"
                                                class="shrink-0 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-white/5">
                                                + Add
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Date
                                                <span class="text-rose-600">*</span></label>
                                            <input type="date" name="transactions_date"
                                                value="{{ $oldFor('transactions_date', 'income') ?: $today }}"
                                                class="{{ $inputClass }}">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Receipt
                                                No</label>
                                            <input type="text" name="recipt_no"
                                                value="{{ $oldFor('recipt_no', 'income') }}"
                                                class="{{ $inputClass }}">
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Note</label>
                                        <input type="text" name="note"
                                            value="{{ $oldFor('note', 'income') }}" class="{{ $inputClass }}">
                                    </div>

                                    <button type="submit"
                                        class="w-full rounded-xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                                        Save Income
                                    </button>
                                </form>
                            </div>

                        </div>

                        {{-- Modal --}}
                        <div x-show="modalOpen" x-cloak
                            class="fixed inset-0 z-50 flex items-center justify-center p-4">
                            <div class="fixed inset-0 bg-slate-900/60" @click="closeModal()"></div>

                            <div
                                class="relative w-full max-w-md rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-white/10 shadow-xl overflow-hidden">
                                <div
                                    class="px-4 py-3 border-b border-slate-200 dark:border-white/10 flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-800 dark:text-slate-100"
                                            x-text="modalTitle"></div>
                                        <div class="text-xs text-slate-500">Create → auto select</div>
                                    </div>
                                    <button type="button" @click="closeModal()"
                                        class="rounded-lg border border-slate-200 dark:border-white/10 px-2 py-1 text-xs hover:bg-slate-50 dark:hover:bg-white/5">✕</button>
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

                                    {{-- ✅ Name (not for students) --}}
                                    <div x-show="modalEntity!=='students'">
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Name</label>
                                        <input type="text" x-model="modalForm.name" class="{{ $inputClass }}"
                                            placeholder="Enter name">
                                    </div>

                                    {{-- ✅ Students (Quick create) --}}
                                    <div x-show="modalEntity==='students'" x-cloak class="space-y-2">
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Full
                                                Name</label>
                                            <input type="text" x-model="modalForm.full_name"
                                                class="{{ $inputClass }}" placeholder="Student full name">
                                        </div>

                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Father
                                                Name</label>
                                            <input type="text" x-model="modalForm.father_name"
                                                class="{{ $inputClass }}" placeholder="Father name (optional)">
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">DOB</label>
                                                <input type="date" x-model="modalForm.dob"
                                                    class="{{ $inputClass }}">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Age</label>
                                                <input type="number" x-model="modalForm.age"
                                                    class="{{ $inputClass }}">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Roll</label>
                                                <input type="number" x-model="modalForm.roll"
                                                    class="{{ $inputClass }}">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Mobile</label>
                                                <input type="text" x-model="modalForm.mobile"
                                                    class="{{ $inputClass }}">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Email
                                                    (optional)</label>
                                                <input type="email" x-model="modalForm.email"
                                                    class="{{ $inputClass }}">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Fees
                                                    Type</label>
                                                <select x-model="modalForm.fees_type_id"
                                                    class="{{ $selectClass }}">
                                                    <option value="">Select</option>
                                                    @foreach ($feesTypes ?? [] as $ft)
                                                        <option value="{{ $ft->id }}">
                                                            {{ $ft->name ?? 'Type #' . $ft->id }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Academic
                                                    Year</label>
                                                <select x-model="modalForm.academic_year_id"
                                                    class="{{ $selectClass }}">
                                                    <option value="">Select</option>
                                                    @foreach ($years ?? [] as $y)
                                                        <option value="{{ $y->id }}">
                                                            {{ $y->name ?? 'Year #' . $y->id }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Class</label>
                                                <select x-model="modalForm.class_id" class="{{ $selectClass }}">
                                                    <option value="">Select</option>
                                                    @foreach ($classes ?? [] as $c)
                                                        <option value="{{ $c->id }}">
                                                            {{ $c->name ?? 'Class #' . $c->id }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Section</label>
                                            <select x-model="modalForm.section_id" class="{{ $selectClass }}">
                                                <option value="">Select</option>
                                                @foreach ($sections ?? [] as $sec)
                                                    <option value="{{ $sec->id }}">
                                                        {{ $sec->name ?? 'Section #' . $sec->id }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <label
                                            class="inline-flex items-center gap-2 text-xs text-slate-700 dark:text-slate-200 mt-1">
                                            <input type="checkbox" x-model="modalForm.isActived"
                                                class="rounded border-slate-300 dark:border-white/20">
                                            Active
                                        </label>
                                    </div>

                                    {{-- Donors --}}
                                    <div x-show="modalEntity==='donors'" x-cloak class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Mobile</label>
                                            <input type="text" x-model="modalForm.mobile"
                                                class="{{ $inputClass }}" placeholder="01xxxxxxxxx">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Email
                                                (optional)</label>
                                            <input type="email" x-model="modalForm.email"
                                                class="{{ $inputClass }}" placeholder="email@example.com">
                                        </div>
                                    </div>

                                    {{-- Accounts --}}
                                    <div x-show="modalEntity==='accounts'" x-cloak>
                                        <label
                                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Account
                                            Details</label>
                                        <input type="text" x-model="modalForm.account_details"
                                            class="{{ $inputClass }}" placeholder="e.g., Cash in hand">
                                        <div class="mt-2">
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Account
                                                Number (optional)</label>
                                            <input type="text" x-model="modalForm.account_number"
                                                class="{{ $inputClass }}" placeholder="Leave blank for auto">
                                        </div>
                                    </div>

                                    {{-- Lenders --}}
                                    <div x-show="modalEntity==='lenders'" x-cloak class="space-y-2">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Phone</label>
                                                <input type="text" x-model="modalForm.phone"
                                                    class="{{ $inputClass }}" placeholder="01xxxxxxxxx">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Email
                                                    (optional)</label>
                                                <input type="email" x-model="modalForm.email"
                                                    class="{{ $inputClass }}" placeholder="email@example.com">
                                            </div>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Address</label>
                                            <input type="text" x-model="modalForm.address"
                                                class="{{ $inputClass }}" placeholder="Address">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Bank
                                                Details</label>
                                            <input type="text" x-model="modalForm.bank_detils"
                                                class="{{ $inputClass }}" placeholder="Bank details">
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-2 pt-2">
                                        <button type="button" @click="closeModal()"
                                            class="rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-white/5">
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
                                            class="px-1 py-0.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded">POST</code>
                                        <span x-text="modalEndpointPreview"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        {{-- /Modal --}}
                    </div>
                </aside>

            </div>
        </div>
    </div>

    {{-- Alpine + JS --}}
    <script>
        function transactionCenterPage() {
            return {
                toast: {
                    show: false,
                    type: 'success',
                    message: ''
                },
                init() {
                    const s = @js(session('success'));
                    const e = @js(session('error'));
                    if (s) this.notify('success', s);
                    if (e) this.notify('error', e);
                },
                notify(type, message) {
                    this.toast.type = type;
                    this.toast.message = message || '';
                    this.toast.show = true;
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 4500);
                }
            }
        }

        function transactionCenterQuickAdd(initialTab = 'student_fee', initialFeeMode = 'single') {
            return {
                tab: initialTab,
                feeMode: initialFeeMode,

                tabClass: 'rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-2 py-2 hover:bg-slate-50 dark:hover:bg-white/5',
                activeTabClass: 'rounded-xl border border-slate-900 bg-slate-900 text-white px-2 py-2 hover:bg-slate-800',

                modalOpen: false,
                modalEntity: null,
                modalTitle: '',
                modalTargetSelectId: null,
                modalErrors: [],
                saving: false,

                modalEndpointPreview: '',

                endpoints: {
                    students: @js($studentQuickEndpoint),
                    // others follow convention /ajax/{entity}
                },

                modalForm: {
                    name: '',
                    mobile: '',
                    email: '',
                    phone: '',
                    address: '',
                    bank_detils: '',
                    account_number: '',
                    account_details: '',

                    full_name: '',
                    father_name: '',
                    dob: '',
                    age: '',
                    roll: '',
                    fees_type_id: '',
                    academic_year_id: '',
                    class_id: '',
                    section_id: '',
                    isActived: true,
                },

                openModal(entity, targetSelectId) {
                    this.modalEntity = entity;
                    this.modalTargetSelectId = targetSelectId || null;
                    this.modalTitle = 'Add ' + entity.replace('_', ' ').replace(/\b\w/g, m => m.toUpperCase());
                    this.modalErrors = [];
                    this.saving = false;

                    const endpoint = this.endpoints?.[entity] || `/ajax/${entity}`;
                    this.modalEndpointPreview = endpoint;

                    this.modalForm = {
                        name: '',
                        mobile: '',
                        email: '',
                        phone: '',
                        address: '',
                        bank_detils: '',
                        account_number: '',
                        account_details: '',

                        full_name: '',
                        father_name: '',
                        dob: '',
                        age: '',
                        roll: '',
                        fees_type_id: '',
                        academic_year_id: '',
                        class_id: '',
                        section_id: '',
                        isActived: true,
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

                _studentLabelFromData(data) {
                    const full = data?.full_name || data?.name || data?.student_name;
                    if (full && data?.roll) return `${full} (Roll: ${data.roll})`;
                    return full || (data?.id ? `Student #${data.id}` : '#');
                },

                async submitModal() {
                    this.modalErrors = [];
                    this.saving = true;

                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                        '{{ csrf_token() }}';
                    const endpoint = this.endpoints?.[this.modalEntity] || `/ajax/${this.modalEntity}`;

                    try {
                        let res;

                        if (this.modalEntity === 'students') {
                            const fd = new FormData();

                            const fullName = (this.modalForm.full_name || '').trim();

                            fd.append('full_name', fullName);
                            fd.append('name', fullName); // backward compatibility
                            fd.append('father_name', this.modalForm.father_name || '');

                            fd.append('dob', this.modalForm.dob || '');
                            fd.append('age', this.modalForm.age || '');
                            fd.append('roll', this.modalForm.roll || '');

                            fd.append('mobile', this.modalForm.mobile || '');
                            fd.append('email', this.modalForm.email || '');

                            fd.append('fees_type_id', this.modalForm.fees_type_id || '');
                            fd.append('academic_year_id', this.modalForm.academic_year_id || '');
                            fd.append('class_id', this.modalForm.class_id || '');
                            fd.append('section_id', this.modalForm.section_id || '');

                            fd.append('isActived', this.modalForm.isActived ? '1' : '0');

                            res = await fetch(endpoint, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrf
                                },
                                body: fd,
                            });
                        } else {
                            let payload = {
                                name: this.modalForm.name
                            };

                            if (this.modalEntity === 'donors') {
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

                            res = await fetch(endpoint, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrf,
                                },
                                body: JSON.stringify(payload),
                            });
                        }

                        const raw = await res.json().catch(() => ({}));

                        if (!res.ok) {
                            if (raw?.errors) this.modalErrors = Object.values(raw.errors).flat();
                            else if (raw?.message) this.modalErrors = [raw.message];
                            else this.modalErrors = ['Something went wrong'];
                            this.saving = false;
                            return;
                        }

                        // handle {ok:true, student:{...}} shape
                        const data = raw?.student ? raw.student : raw;
                        const newId = data?.id;

                        if (!newId) {
                            this.modalErrors = ['Invalid response (missing id)'];
                            this.saving = false;
                            return;
                        }

                        // append option to ALL selects for this entity
                        const entityKey = this.modalEntity;
                        const selects = document.querySelectorAll(`select[data-entity-select="${entityKey}"]`);

                        selects.forEach(sel => {
                            const opt = document.createElement('option');
                            opt.value = newId;

                            if (entityKey === 'students') {
                                opt.textContent = this._studentLabelFromData(data);

                                opt.dataset.roll = data.roll ?? '';
                                opt.dataset.classId = data.class_id ?? '';
                                opt.dataset.sectionId = data.section_id ?? '';
                                opt.dataset.academicYearId = data.academic_year_id ?? '';
                                opt.dataset.feesTypeId = data.fees_type_id ?? '';
                            } else {
                                opt.textContent = data.name || data.title || (`#${newId}`);
                            }

                            sel.appendChild(opt);
                        });

                        // auto-select target
                        if (this.modalTargetSelectId) {
                            const target = document.getElementById(this.modalTargetSelectId);
                            if (target) {
                                target.value = String(newId);
                                target.dispatchEvent(new Event('change', {
                                    bubbles: true
                                }));
                            }
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

    {{-- Student meta auto-fill --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const studentSel = document.getElementById('select_student_fee_student');
            if (!studentSel) return;

            const rollInput = document.getElementById('student_fee_roll');

            const setIfEmpty = (el, val) => {
                if (!el || !val) return false;
                if (!el.value) {
                    el.value = String(val);
                    el.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                    return true;
                }
                return false;
            };

            const applyMeta = () => {
                const opt = studentSel.selectedOptions?.[0];
                if (!opt) return;

                if (rollInput) rollInput.value = opt.dataset.roll || '';

                setIfEmpty(document.getElementById('select_student_fee_year'), opt.dataset.academicYearId);
                setIfEmpty(document.getElementById('select_student_fee_class'), opt.dataset.classId);
                setIfEmpty(document.getElementById('select_student_fee_section'), opt.dataset.sectionId);
                setIfEmpty(document.getElementById('select_student_fee_fess_type'), opt.dataset.feesTypeId);
            };

            studentSel.addEventListener('change', applyMeta);
            applyMeta();
        });
    </script>

    {{-- Class default fees AJAX --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('tc_single_fee_form');
            if (!form) return;

            const endpoint = @js($classDefaultFeesEndpoint);

            const classSel = document.getElementById('select_student_fee_class');
            const feesTypeSel = document.getElementById('select_student_fee_fess_type');
            const applyBtn = document.getElementById('tc_apply_class_defaults');
            const statusEl = document.getElementById('tc_defaults_status');

            if (!classSel) return;

            const setStatus = (t) => {
                if (statusEl) statusEl.textContent = t || '';
            };

            const setFee = (name, val, force = false) => {
                const el = form.querySelector(`input[name="${name}"]`);
                if (!el) return;

                const incoming = parseFloat(val || 0) || 0;

                const currentRaw = String(el.value || '').trim();
                const currentNum = parseFloat(currentRaw || 0) || 0;

                if (!force) {
                    if (currentRaw !== '' && currentNum > 0) return;
                }

                el.value = incoming > 0 ? String(incoming) : '';
                el.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            };

            const loadDefaults = async (force = false) => {
                const classId = classSel.value;
                if (!classId) {
                    setStatus('');
                    return;
                }

                setStatus('Loading defaults...');
                try {
                    const url = new URL(endpoint, window.location.origin);
                    url.searchParams.set('class_id', classId);

                    const res = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json().catch(() => ({}));

                    if (!res.ok || !data?.found) {
                        setStatus('No defaults found for this class.');
                        return;
                    }

                    setFee('monthly_fees', data.monthly_fees, force);
                    setFee('boarding_fees', data.boarding_fees, force);
                    setFee('management_fees', data.management_fees, force);
                    setFee('exam_fees', data.exam_fees, force);
                    setFee('others_fees', data.others_fees, force);

                    setStatus(`Defaults applied (source #${data.source_id}).`);
                } catch (e) {
                    setStatus('Failed to load defaults.');
                }
            };

            classSel.addEventListener('change', () => loadDefaults(false));
            feesTypeSel?.addEventListener('change', () => loadDefaults(false));
            applyBtn?.addEventListener('click', () => loadDefaults(true));

            if (classSel.value) loadDefaults(false);
        });
    </script>

    {{-- Total auto-calc (single fee) --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mode = document.querySelector('input[name="_tc_fee_mode"][value="single"]');
            const form = mode ? mode.closest('form') : null;
            if (!form) return;

            const feeNames = ['admission_fee', 'monthly_fees', 'boarding_fees', 'management_fees', 'exam_fees',
                'others_fees'
            ];
            const totalEl = form.querySelector('input[name="total_fees"]');

            const getNum = (name) => {
                const el = form.querySelector(`input[name="${name}"]`);
                return el ? (parseFloat(el.value || 0) || 0) : 0;
            };

            const calc = () => {
                let sum = 0;
                feeNames.forEach(n => sum += getNum(n));
                if (totalEl) totalEl.value = sum > 0 ? sum.toFixed(2) : '';
            };

            feeNames.forEach(name => {
                const el = form.querySelector(`input[name="${name}"]`);
                if (el) el.addEventListener('input', calc);
            });

            calc();
        });
    </script>

    {{-- Date range buttons --}}
    <script>
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
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-app-layout>
