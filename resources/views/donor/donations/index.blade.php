@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
@endphp

<x-donor-layout
    title="Donation History"
    description="This page lists only your own donation rows from the existing ledger. New write flows stay outside the portal in Phase 3."
>
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Donation Rows</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $summary['donation_count'] }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Total Given</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $money($summary['donation_total']) }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Latest Donation</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $summary['latest_donation_at'] ?: '-' }}</div>
        </div>
    </div>

    <section class="mt-6 overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10 text-sm">
                <thead class="bg-white/5 text-left text-xs uppercase tracking-[0.2em] text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Account</th>
                        <th class="px-6 py-4">Note</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($donations as $donation)
                        <tr class="text-slate-300">
                            <td class="px-6 py-4 font-semibold text-white">{{ $donation->c_s_1 ?: 'Donation #'.$donation->id }}</td>
                            <td class="px-6 py-4">{{ $donation->account?->name ?: 'Account not set' }}</td>
                            <td class="px-6 py-4">{{ $donation->note ?: '-' }}</td>
                            <td class="px-6 py-4">{{ $money($donation->credit) }} BDT</td>
                            <td class="px-6 py-4">{{ $donation->transactions_date ?: optional($donation->created_at)->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-400">
                                No donation history has been recorded for this donor profile yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($donations->hasPages())
            <div class="border-t border-white/10 px-6 py-4">
                {{ $donations->links() }}
            </div>
        @endif
    </section>
</x-donor-layout>
