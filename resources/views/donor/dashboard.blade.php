@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
@endphp

<x-donor-layout
    title="Donor Dashboard"
    description="Review your donation and receipt history in a read-only portal. Online donation initiation remains blocked until the later payment phase."
>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
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

        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Receipts</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $summary['receipt_count'] }}</div>
        </div>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-[0.9fr,1.1fr]">
        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
            <h2 class="text-lg font-semibold text-white">Donor Profile</h2>
            <dl class="mt-5 space-y-4 text-sm text-slate-300">
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Name</dt>
                    <dd class="mt-1 text-base text-white">{{ $donor->name ?: Auth::user()->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Email</dt>
                    <dd class="mt-1">{{ $donor->email ?: Auth::user()->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Mobile</dt>
                    <dd class="mt-1">{{ $donor->mobile ?: 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Address</dt>
                    <dd class="mt-1 leading-6">{{ $donor->address ?: 'No donor portal address has been added yet.' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Portal Notes</dt>
                    <dd class="mt-1 leading-6">{{ $donor->notes ?: 'This phase is read-only. Legacy donation entry and payment finalization stay outside the donor portal.' }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-[2rem] border border-white/10 bg-slate-900/70">
            <div class="flex items-center justify-between border-b border-white/10 px-6 py-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">Recent Donations</h2>
                    <p class="mt-1 text-sm text-slate-400">Only donation rows recorded against your linked donor profile appear here.</p>
                </div>
                <a href="{{ route('donor.donations.index') }}" class="text-sm font-medium text-orange-300">View all</a>
            </div>

            <div class="divide-y divide-white/10">
                @forelse ($recentDonations as $donation)
                    <div class="px-6 py-5">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <div class="font-semibold text-white">{{ $donation->c_s_1 ?: 'Donation #'.$donation->id }}</div>
                                <div class="mt-1 text-sm text-slate-400">
                                    {{ $donation->note ?: 'Donation record' }}
                                </div>
                            </div>
                            <div class="text-sm text-slate-300">
                                <div>{{ $money($donation->credit) }} BDT</div>
                                <div class="mt-1 text-slate-400">
                                    {{ $donation->transactions_date ?: optional($donation->created_at)->format('Y-m-d') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-sm text-slate-400">
                        No donation history has been recorded for this donor profile yet.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <section class="mt-8 rounded-[2rem] border border-white/10 bg-slate-900/70">
        <div class="flex items-center justify-between border-b border-white/10 px-6 py-5">
            <div>
                <h2 class="text-lg font-semibold text-white">Recent Receipts</h2>
                <p class="mt-1 text-sm text-slate-400">Receipt visibility is user-bound and remains read-only in this phase.</p>
            </div>
            <a href="{{ route('donor.receipts.index') }}" class="text-sm font-medium text-sky-300">Receipt history</a>
        </div>

        <div class="divide-y divide-white/10">
            @forelse ($recentReceipts as $receipt)
                <div class="px-6 py-5">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="font-semibold text-white">{{ $receipt->receipt_number }}</div>
                            <div class="mt-1 text-sm text-slate-400">
                                {{ strtoupper($receipt->payment?->status ?: 'recorded') }} via {{ $receipt->payment?->provider ?: 'manual' }}
                            </div>
                        </div>
                        <div class="text-sm text-slate-300">
                            <div>{{ $money($receipt->amount) }} {{ $receipt->currency ?: 'BDT' }}</div>
                            <div class="mt-1 text-slate-400">{{ optional($receipt->issued_at ?? $receipt->created_at)->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-10 text-sm text-slate-400">
                    No donor-visible receipts are available yet.
                </div>
            @endforelse
        </div>
    </section>
</x-donor-layout>
