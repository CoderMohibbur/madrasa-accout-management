@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
@endphp

<x-donor-layout
    title="Receipt History"
    description="Receipt visibility stays limited to records tied to your portal user. Phase 3 does not add live gateway callbacks or donor-side payment writes."
>
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Receipts</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $summary['receipt_count'] }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Total Receipted</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $money($summary['receipt_total']) }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Latest Receipt</div>
            <div class="mt-3 text-3xl font-semibold text-white">
                {{ $summary['latest_receipt_at'] ? \Illuminate\Support\Carbon::parse($summary['latest_receipt_at'])->format('Y-m-d H:i') : '-' }}
            </div>
        </div>
    </div>

    <section class="mt-6 overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10 text-sm">
                <thead class="bg-white/5 text-left text-xs uppercase tracking-[0.2em] text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Receipt</th>
                        <th class="px-6 py-4">Provider</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Reference</th>
                        <th class="px-6 py-4">Issued</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($receipts as $receipt)
                        <tr class="text-slate-300">
                            <td class="px-6 py-4 font-semibold text-white">{{ $receipt->receipt_number }}</td>
                            <td class="px-6 py-4">{{ $receipt->payment?->provider ?: 'manual' }}</td>
                            <td class="px-6 py-4">{{ strtoupper($receipt->payment?->status ?: 'recorded') }}</td>
                            <td class="px-6 py-4">{{ $money($receipt->amount) }} {{ $receipt->currency ?: 'BDT' }}</td>
                            <td class="px-6 py-4">{{ $receipt->payment?->provider_reference ?: $receipt->payment?->idempotency_key ?: '-' }}</td>
                            <td class="px-6 py-4">{{ optional($receipt->issued_at ?? $receipt->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-400">
                                No donor-visible receipts are available yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($receipts->hasPages())
            <div class="border-t border-white/10 px-6 py-4">
                {{ $receipts->links() }}
            </div>
        @endif
    </section>
</x-donor-layout>
