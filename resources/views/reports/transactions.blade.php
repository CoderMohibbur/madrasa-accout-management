@php $pageTitle = 'Transactions Report'; @endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $pageTitle }}</h2>
                <p class="text-xs text-slate-500 mt-1">Filter + export CSV</p>
            </div>
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            <form method="GET" action="{{ route('reports.transactions') }}"
                  class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4">
                <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">From</label>
                        <input type="date" name="from" value="{{ $from }}"
                               class="w-full rounded-xl border-slate-200 text-sm">
                    </div>
                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">To</label>
                        <input type="date" name="to" value="{{ $to }}"
                               class="w-full rounded-xl border-slate-200 text-sm">
                    </div>
                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Account</label>
                        <select name="account_id" class="w-full rounded-xl border-slate-200 text-sm">
                            <option value="">All</option>
                            @foreach($accounts ?? [] as $a)
                                <option value="{{ $a->id }}" @selected((string)$a->id === (string)request('account_id'))>
                                    {{ $a->name ?? 'Account #'.$a->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Type</label>
                        <select name="type_id" class="w-full rounded-xl border-slate-200 text-sm">
                            <option value="">All</option>
                            @foreach($types ?? [] as $t)
                                <option value="{{ $t->id }}" @selected((string)$t->id === (string)request('type_id'))>
                                    {{ $t->name ?? 'Type #'.$t->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-9">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="w-full rounded-xl border-slate-200 text-sm"
                               placeholder="receipt / note / keyword">
                    </div>

                    <div class="col-span-12 sm:col-span-3 flex items-end gap-2">
                        <button class="w-full rounded-xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                            Apply
                        </button>

                        <a class="w-full text-center rounded-xl border border-slate-200 bg-white text-sm px-4 py-2 hover:bg-slate-50"
                           href="{{ route('reports.transactions.csv', request()->query()) }}">
                            CSV
                        </a>
                    </div>
                </div>
            </form>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                    <div class="text-sm font-semibold text-slate-800">Transactions</div>
                    <div class="text-xs text-slate-500">Total: {{ $transactions->total() }}</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">Date</th>
                                <th class="px-4 py-3 text-left">Type</th>
                                <th class="px-4 py-3 text-left">Party</th>
                                <th class="px-4 py-3 text-left">Account</th>
                                <th class="px-4 py-3 text-right">Debit</th>
                                <th class="px-4 py-3 text-right">Credit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($transactions as $tx)
                                @php
                                    $typeName = data_get($tx,'type.name') ?? ('Type #'.$tx->transactions_type_id);
                                    $studentName = data_get($tx,'student.name') ?? data_get($tx,'student.student_name');
                                    $donorName = data_get($tx,'donor.name') ?? data_get($tx,'donor.donor_name') ?? data_get($tx,'donor.doner_name');
                                    $lenderName = data_get($tx,'lender.name') ?? data_get($tx,'lender.lender_name');
                                    $party = $studentName ?: ($donorName ?: ($lenderName ?: '-'));
                                    $accountName = data_get($tx,'account.name') ?? ('Account #'.$tx->account_id);
                                @endphp
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="font-medium text-slate-800">{{ $tx->transactions_date }}</div>
                                        <div class="text-xs text-slate-500">#{{ $tx->id }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ $typeName }}</td>
                                    <td class="px-4 py-3">{{ $party }}</td>
                                    <td class="px-4 py-3">{{ $accountName }}</td>
                                    <td class="px-4 py-3 text-right text-emerald-700 font-semibold">{{ number_format((float)($tx->debit ?? 0),2) }}</td>
                                    <td class="px-4 py-3 text-right text-rose-700 font-semibold">{{ number_format((float)($tx->credit ?? 0),2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">No data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-slate-200">
                    {{ $transactions->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
