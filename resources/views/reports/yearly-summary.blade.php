<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="text-lg font-semibold text-slate-900">Yearly Summary</div>
                <div class="text-xs text-slate-500">Month-wise totals • Click a month for details</div>
            </div>
            <a href="{{ route('reports.monthly-statement') }}"
               class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                Monthly Statement
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            <form method="GET" action="{{ route('reports.yearly-summary') }}"
                  class="bg-white border border-slate-200 rounded-2xl p-4">
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
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" @selected((string)$accountId === (string)$acc->id)>
                                    {{ $acc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-2 flex items-end">
                        <button class="w-full rounded-xl bg-emerald-600 text-white text-sm px-4 py-2 hover:bg-emerald-700">
                            Apply
                        </button>
                    </div>
                </div>
            </form>

            <div class="bg-white border border-slate-200 rounded-2xl p-4">
                <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-12 sm:col-span-4">
                        <div class="text-xs text-slate-500">Year Total Income (Credit)</div>
                        <div class="text-xl font-extrabold text-emerald-700">{{ number_format($yearIncome, 2) }}</div>
                    </div>
                    <div class="col-span-12 sm:col-span-4">
                        <div class="text-xs text-slate-500">Year Total Expense (Debit)</div>
                        <div class="text-xl font-extrabold text-rose-700">{{ number_format($yearExpense, 2) }}</div>
                    </div>
                    <div class="col-span-12 sm:col-span-4">
                        <div class="text-xs text-slate-500">Year Surplus (Credit − Debit)</div>
                        <div class="text-xl font-extrabold {{ ($yearIncome-$yearExpense)>=0 ? 'text-emerald-700':'text-rose-700' }}">
                            {{ number_format($yearIncome-$yearExpense, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-3">
                @foreach($months as $m)
                    <a href="{{ route('reports.monthly-statement', array_filter([
                        'month' => $m['ym'],
                        'account_id' => $accountId
                    ])) }}"
                       class="col-span-12 sm:col-span-6 lg:col-span-3 bg-white border border-slate-200 rounded-2xl p-4 hover:bg-slate-50">
                        <div class="text-sm font-semibold text-slate-900">{{ $m['label'] }}</div>
                        <div class="mt-2 text-xs text-slate-500">Income (Credit)</div>
                        <div class="text-lg font-extrabold text-emerald-700">{{ number_format($m['income'], 2) }}</div>

                        <div class="mt-2 text-xs text-slate-500">Expense (Debit)</div>
                        <div class="text-lg font-extrabold text-rose-700">{{ number_format($m['expense'], 2) }}</div>

                        <div class="mt-2 text-xs text-slate-500">Surplus</div>
                        <div class="text-sm font-bold {{ $m['surplus']>=0 ? 'text-emerald-700':'text-rose-700' }}">
                            {{ number_format($m['surplus'], 2) }}
                        </div>
                    </a>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>