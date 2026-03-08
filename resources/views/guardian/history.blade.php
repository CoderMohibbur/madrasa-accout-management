@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
@endphp

<x-guardian-layout
    title="Payment History"
    description="This page shows guardian-visible payment rows and their receipt numbers without enabling any new payment write flow."
>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Payment Rows</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $summary['payment_count'] }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Paid Rows</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $summary['paid_count'] }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Receipts</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $summary['receipt_count'] }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Verified Paid Total</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $money($summary['paid_total']) }}</div>
        </div>
    </div>

    <section class="mt-6 overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10 text-sm">
                <thead class="bg-white/5 text-left text-xs uppercase tracking-[0.2em] text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Invoice</th>
                        <th class="px-6 py-4">Student</th>
                        <th class="px-6 py-4">Provider</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Receipt</th>
                        <th class="px-6 py-4">Reference</th>
                        <th class="px-6 py-4">Recorded</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($payments as $payment)
                        <tr class="text-slate-300">
                            <td class="px-6 py-4">
                                <a href="{{ route('guardian.invoices.show', $payment->payable) }}" class="font-semibold text-white hover:text-emerald-300">
                                    {{ $payment->payable?->invoice_number ?: 'Payment #'.$payment->id }}
                                </a>
                            </td>
                            <td class="px-6 py-4">{{ $payment->payable?->student?->full_name ?: 'Linked student' }}</td>
                            <td class="px-6 py-4">{{ $payment->provider ?: 'manual' }}</td>
                            <td class="px-6 py-4">{{ strtoupper($payment->status) }}</td>
                            <td class="px-6 py-4">{{ $money($payment->amount) }}</td>
                            <td class="px-6 py-4">{{ $payment->receipt?->receipt_number ?: 'Receipt pending' }}</td>
                            <td class="px-6 py-4">{{ $payment->provider_reference ?: $payment->idempotency_key }}</td>
                            <td class="px-6 py-4">
                                {{ optional($payment->paid_at ?? $payment->initiated_at ?? $payment->created_at)->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-slate-400">
                                No guardian-visible payment history exists yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payments->hasPages())
            <div class="border-t border-white/10 px-6 py-4">
                {{ $payments->links() }}
            </div>
        @endif
    </section>
</x-guardian-layout>
