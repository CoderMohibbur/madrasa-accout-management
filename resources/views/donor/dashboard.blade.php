@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
@endphp

<x-donor-layout
    title="Donor Dashboard"
    description="Review legacy donor entries and the new online donor-domain records together in one read-only portal."
>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card
            label="Donation rows"
            value="{{ $summary['donation_count'] }}"
            meta="Legacy and online donor records stay visible without changing their provenance."
        />
        <x-ui.stat-card
            label="Total given"
            value="{{ $money($summary['donation_total']) }}"
            meta="Read-only donor totals remain separate from new settlement posting."
        />
        <x-ui.stat-card
            label="Latest donation"
            value="{{ $summary['latest_donation_at'] ? $summary['latest_donation_at']->format('Y-m-d') : '-' }}"
            meta="Most recent donor-visible activity in this portal."
        />
        <x-ui.stat-card
            label="Receipts"
            value="{{ $summary['receipt_count'] }}"
            meta="Only donor-scoped receipts appear here."
        />
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-[0.9fr,1.1fr]">
        <x-ui.card
            title="Donor profile"
            description="Profile details stay informational and read-only in this first donor portal rollout."
        >
            <dl class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Name</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $donor->name ?: Auth::user()->name }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Email</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $donor->email ?: Auth::user()->email }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Mobile</dt>
                    <dd class="mt-2 text-sm text-slate-700">{{ $donor->mobile ?: 'Not provided' }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Address</dt>
                    <dd class="mt-2 text-sm leading-6 text-slate-700">{{ $donor->address ?: 'No donor portal address has been added yet.' }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3 sm:col-span-2">
                    <dt class="ui-stat-label">Portal notes</dt>
                    <dd class="mt-2 text-sm leading-6 text-slate-700">{{ $donor->notes ?: 'This phase is read-only. Legacy donation entry and payment finalization stay outside the donor portal.' }}</dd>
                </div>
            </dl>
        </x-ui.card>

        <x-ui.card
            title="Recent donations"
            description="Portal-eligible donor history keeps legacy records and new online donations separate but visible together."
        >
            <x-slot name="headerActions">
                <a href="{{ route('donor.donations.index') }}" class="ui-button ui-button--secondary">View all</a>
            </x-slot>

            @if ($recentDonations->isEmpty())
                <x-ui.empty-state
                    title="No donation history yet"
                    description="No donation history has been recorded for this donor profile yet."
                />
            @else
                <div class="ui-list-divider">
                    @foreach ($recentDonations as $donation)
                        <div class="ui-list-row">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="font-semibold text-slate-950">{{ data_get($donation, 'title') }}</div>
                                    <x-ui.badge variant="info">{{ data_get($donation, 'source_label') }}</x-ui.badge>
                                </div>
                                <div class="mt-1 text-sm text-slate-600">
                                    {{ data_get($donation, 'note') }}
                                </div>
                                <div class="mt-2 flex flex-wrap gap-2 text-xs uppercase tracking-[0.16em] text-slate-500">
                                    <span>{{ data_get($donation, 'reference') }}</span>
                                    <span>{{ data_get($donation, 'account_name') }}</span>
                                    <span>{{ data_get($donation, 'display_label') }}</span>
                                </div>
                            </div>
                            <div class="text-sm text-slate-700">
                                <div class="font-semibold text-slate-950">{{ $money(data_get($donation, 'amount')) }} {{ data_get($donation, 'currency', 'BDT') }}</div>
                                <div class="mt-1 text-slate-500">
                                    {{ optional(data_get($donation, 'occurred_at'))->format('Y-m-d H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>

    <div class="mt-8">
        <x-ui.card
            title="Recent receipts"
            description="Only donor-scoped receipts appear here; guest and transaction-specific access stays separate."
        >
            <x-slot name="headerActions">
                <a href="{{ route('donor.receipts.index') }}" class="ui-button ui-button--secondary">Receipt history</a>
            </x-slot>

            @if ($recentReceipts->isEmpty())
                <x-ui.empty-state
                    title="No donor-visible receipts yet"
                    description="No donor-visible receipts are available yet."
                />
            @else
                <div class="ui-list-divider">
                    @foreach ($recentReceipts as $receipt)
                        <div class="ui-list-row">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="font-semibold text-slate-950">{{ data_get($receipt, 'receipt_number') }}</div>
                                    <x-ui.badge variant="warning">{{ data_get($receipt, 'source_label') }}</x-ui.badge>
                                </div>
                                <div class="mt-1 text-sm text-slate-600">
                                    {{ data_get($receipt, 'status_label') }} via {{ data_get($receipt, 'provider') }}
                                </div>
                            </div>
                            <div class="text-sm text-slate-700">
                                <div class="font-semibold text-slate-950">{{ $money(data_get($receipt, 'amount')) }} {{ data_get($receipt, 'currency', 'BDT') }}</div>
                                <div class="mt-1 text-slate-500">{{ optional(data_get($receipt, 'issued_at'))->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>
</x-donor-layout>
