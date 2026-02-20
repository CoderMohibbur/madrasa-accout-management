@php
    $getAmount = function ($tx) {
        $d = (float) ($tx->debit ?? 0);
        $c = (float) ($tx->credit ?? 0);
        return $d > 0 ? $d : $c;
    };

    $getParty = function ($tx) {
        $studentName =
            data_get($tx, 'student.name') ??
            (data_get($tx, 'student.student_name') ?? data_get($tx, 'student.full_name'));
        $donorName =
            data_get($tx, 'donor.name') ??
            (data_get($tx, 'donor.donor_name') ?? data_get($tx, 'donor.doner_name'));
        $lenderName = data_get($tx, 'lender.name') ?? data_get($tx, 'lender.lender_name');

        if ($studentName) return ['label' => 'Student', 'name' => $studentName];
        if ($donorName) return ['label' => 'Donor', 'name' => $donorName];
        if ($lenderName) return ['label' => 'Lender', 'name' => $lenderName];

        return ['label' => '-', 'name' => '-'];
    };

    $party = $getParty($transaction);
    $amount = $getAmount($transaction);
    $typeName = data_get($transaction, 'type.name') ?? '-';
    $accountName = data_get($transaction, 'account.name') ?? ('Account #' . ($transaction->account_id ?? '-'));
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">Receipt</h2>
                <p class="text-xs text-slate-500 mt-1">Transaction #{{ $transaction->id }}</p>
            </div>

            <div class="print:hidden flex items-center gap-2">
                <a href="{{ url('/transaction-center') }}"
                    class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm hover:bg-slate-50">
                    Back
                </a>
                <button onclick="window.print()"
                    class="inline-flex items-center rounded-xl bg-slate-900 px-3 py-2 text-sm text-white hover:bg-slate-800">
                    Print
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-lg font-semibold text-slate-800">Receipt</div>
                        <div class="text-xs text-slate-500">Generated from Transaction Center</div>
                    </div>
                    <div class="text-right text-xs text-slate-600">
                        <div><span class="font-medium">Date:</span> {{ $transaction->transactions_date ?? '-' }}</div>
                        <div><span class="font-medium">Receipt No:</span> {{ $transaction->recipt_no ?? '-' }}</div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="text-xs text-slate-500">Type</div>
                        <div class="font-semibold text-slate-800">{{ $typeName }}</div>
                    </div>

                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="text-xs text-slate-500">Account</div>
                        <div class="font-semibold text-slate-800">{{ $accountName }}</div>
                    </div>

                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="text-xs text-slate-500">Party</div>
                        <div class="font-semibold text-slate-800">{{ $party['name'] }}</div>
                        <div class="text-xs text-slate-500">{{ $party['label'] }}</div>
                    </div>

                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="text-xs text-slate-500">Amount</div>
                        <div class="text-xl font-bold text-slate-900">{{ number_format((float)$amount, 2) }}</div>
                        <div class="text-xs text-slate-500">
                            {{ ((float)($transaction->debit ?? 0)) > 0 ? 'Debit (cash in)' : 'Credit (cash out)' }}
                        </div>
                    </div>
                </div>

                @if($transaction->note)
                    <div class="mt-4 rounded-xl border border-slate-200 p-4 text-sm">
                        <div class="text-xs text-slate-500 mb-1">Note</div>
                        <div class="text-slate-800">{{ $transaction->note }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @media print {
            .print\:hidden { display: none !important; }
        }
    </style>
</x-app-layout>
