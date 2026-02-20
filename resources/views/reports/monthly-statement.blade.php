{{-- resources/views/reports/monthly-statement.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="text-lg font-semibold text-slate-900">Monthly Statement</div>
                <div class="text-xs text-slate-500">
                    Paper format • Income (Credit) vs Expense (Debit) • Bangla supported
                </div>
            </div>
            <button type="button" onclick="window.print()"
                class="print:hidden rounded-xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                Print / Save PDF
            </button>
        </div>
    </x-slot>

    <style>
        @media print {
            .print\:hidden { display: none !important; }
            body { background: #fff !important; }
            .print-area { padding: 0 !important; }
        }
        @page { size: A4; margin: 12mm; }
    </style>

    <div class="py-6 print-area">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Filters --}}
            <div class="print:hidden bg-white border border-slate-200 dark:border-white/10 rounded-3xl p-4">
                <form method="GET" action="{{ route('reports.monthly-statement') }}" class="grid grid-cols-12 gap-3">
                    <div class="col-span-12 sm:col-span-4">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Month</label>
                        <input type="month" name="month" value="{{ $month }}"
                            class="w-full rounded-xl border-slate-200 dark:border-white/10 text-sm
                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div class="col-span-12 sm:col-span-6">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Account (Optional)</label>
                        <select name="account_id"
                            class="w-full rounded-xl border-slate-200 dark:border-white/10 text-sm
                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All Accounts</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" @selected((string)$accountId === (string)$acc->id)>
                                    {{ $acc->name ?? ('Account #'.$acc->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-2 flex items-end">
                        <button type="submit"
                            class="w-full rounded-xl bg-emerald-600 text-white text-sm px-4 py-2 hover:bg-emerald-700">
                            Apply
                        </button>
                    </div>
                </form>
            </div>

            {{-- Paper Header --}}
            <div class="bg-white border border-slate-200 dark:border-white/10 rounded-3xl p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-xl font-extrabold text-slate-900">At Tawhid Madrasha</div>
                        <div class="text-sm text-slate-600">Monthly Statement ({{ $month }})</div>
                        <div class="text-xs text-slate-500">From {{ $start }} to {{ $end }}</div>
                    </div>

                    <div class="text-right">
                        <div class="text-xs text-slate-500">Generated</div>
                        <div class="text-sm font-semibold text-slate-900">{{ now()->format('Y-m-d H:i') }}</div>
                        @if($accountId)
                            <div class="text-xs text-slate-500 mt-1">Account filter applied</div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-12 gap-4">

                    {{-- ✅ Income (Credit) --}}
                    <div class="col-span-12 lg:col-span-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-semibold text-slate-900">Income (Credit)</div>
                            <div class="text-sm font-semibold text-emerald-700">{{ number_format((float)$totalIncome, 2) }}</div>
                        </div>

                        <div class="mt-2 overflow-x-auto border border-slate-200 rounded-2xl">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Date</th>
                                        <th class="px-3 py-2 text-left">Details</th>
                                        <th class="px-3 py-2 text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($incomeTx as $tx)
                                        @php
                                            $amt = (float)($tx->credit ?? 0);   // ✅ FIX
                                            $disp = $getDisplay($tx);
                                            $typeName = data_get($tx, 'type.name') ?? ('Type #'.($tx->transactions_type_id ?? ''));
                                            $note = $tx->note ?? $tx->remarks ?? null;
                                        @endphp
                                        <tr>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <div class="font-medium text-slate-900">{{ $tx->transactions_date ?? '-' }}</div>
                                                <div class="text-[11px] text-slate-500">#{{ $tx->id }}</div>
                                            </td>
                                            <td class="px-3 py-2">
                                                <div class="text-slate-900 font-medium">{{ $typeName }}</div>
                                                <div class="text-xs text-slate-600">
                                                    @if($disp['party_name'])
                                                        <span class="font-semibold">{{ $disp['party_label'] }}:</span> {{ $disp['party_name'] }}
                                                    @endif
                                                    @if($disp['title'])
                                                        <span class="ml-1 text-slate-500">•</span> {{ $disp['title'] }}
                                                    @endif
                                                </div>
                                                @if($note)
                                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $note }}</div>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-right font-semibold text-emerald-700 whitespace-nowrap">
                                                {{ number_format($amt, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-3 py-8 text-center text-slate-500">
                                                No income found for this month.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ✅ Expense (Debit) --}}
                    <div class="col-span-12 lg:col-span-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-semibold text-slate-900">Expense (Debit)</div>
                            <div class="text-sm font-semibold text-rose-700">{{ number_format((float)$totalExpense, 2) }}</div>
                        </div>

                        @foreach($expenseGroups as $bucket => $items)
                            @php
                                $bucketTotal = (float) $items->sum(fn($t) => (float)($t->debit ?? 0)); // ✅ FIX
                            @endphp

                            <div class="mt-3">
                                <div class="flex items-center justify-between">
                                    <div class="text-xs font-semibold text-slate-700">{{ $bucket }}</div>
                                    <div class="text-xs font-semibold text-rose-700">{{ number_format($bucketTotal, 2) }}</div>
                                </div>

                                <div class="mt-2 overflow-x-auto border border-slate-200 rounded-2xl">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                                            <tr>
                                                <th class="px-3 py-2 text-left">Date</th>
                                                <th class="px-3 py-2 text-left">Details</th>
                                                <th class="px-3 py-2 text-right">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach($items as $tx)
                                                @php
                                                    $amt = (float)($tx->debit ?? 0); // ✅ FIX
                                                    $disp = $getDisplay($tx);
                                                    $typeName = data_get($tx, 'type.name') ?? ('Type #'.($tx->transactions_type_id ?? ''));
                                                    $note = $tx->note ?? $tx->remarks ?? null;
                                                @endphp
                                                <tr>
                                                    <td class="px-3 py-2 whitespace-nowrap">
                                                        <div class="font-medium text-slate-900">{{ $tx->transactions_date ?? '-' }}</div>
                                                        <div class="text-[11px] text-slate-500">#{{ $tx->id }}</div>
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <div class="text-slate-900 font-medium">{{ $typeName }}</div>
                                                        <div class="text-xs text-slate-600">
                                                            @if($disp['party_name'])
                                                                <span class="font-semibold">{{ $disp['party_label'] }}:</span> {{ $disp['party_name'] }}
                                                            @endif
                                                            @if($disp['title'])
                                                                <span class="ml-1 text-slate-500">•</span> {{ $disp['title'] }}
                                                            @endif
                                                        </div>
                                                        @if($note)
                                                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $note }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2 text-right font-semibold text-rose-700 whitespace-nowrap">
                                                        {{ number_format($amt, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach

                        @if($expenseGroups->isEmpty())
                            <div class="mt-2 border border-slate-200 rounded-2xl p-6 text-center text-slate-500">
                                No expense found for this month.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Totals --}}
                <div class="mt-6 grid grid-cols-12 gap-3">
                    <div class="col-span-12 lg:col-span-4 border border-slate-200 rounded-2xl p-4">
                        <div class="text-xs text-slate-500">Total Income (Credit)</div>
                        <div class="text-xl font-extrabold text-emerald-700">{{ number_format((float)$totalIncome, 2) }}</div>
                    </div>
                    <div class="col-span-12 lg:col-span-4 border border-slate-200 rounded-2xl p-4">
                        <div class="text-xs text-slate-500">Total Expense (Debit)</div>
                        <div class="text-xl font-extrabold text-rose-700">{{ number_format((float)$totalExpense, 2) }}</div>
                    </div>
                    <div class="col-span-12 lg:col-span-4 border border-slate-200 rounded-2xl p-4">
                        <div class="text-xs text-slate-500">Surplus / Deficit (Credit − Debit)</div>
                        <div class="text-xl font-extrabold {{ $surplus >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ number_format((float)$surplus, 2) }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>