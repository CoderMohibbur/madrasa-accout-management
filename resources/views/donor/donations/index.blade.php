@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
@endphp

<x-donor-layout
    title="Donation History"
    description="Portal-eligible donor history keeps legacy donor entries and new online donor-domain records visible together without merging their provenance."
>
    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.stat-card label="Donation rows" value="{{ $summary['donation_count'] }}" meta="Total donor-visible records in this history view." />
        <x-ui.stat-card label="Total given" value="{{ $money($summary['donation_total']) }}" meta="Read-only total across legacy and online donor records." />
        <x-ui.stat-card label="Latest donation" value="{{ $summary['latest_donation_at'] ? $summary['latest_donation_at']->format('Y-m-d') : '-' }}" meta="Most recent donor-visible donation date." />
    </div>

    <div class="mt-6">
        <x-ui.table
            title="Donation history"
            description="Legacy donor records and new online donor-domain records remain visible together without rewriting their source labels."
        >
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Source</th>
                    <th>Reference</th>
                    <th>Note</th>
                    <th data-numeric="true">Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($donations as $donation)
                    <tr>
                        <td class="font-semibold text-slate-950">{{ data_get($donation, 'title') }}</td>
                        <td>
                            <div>{{ data_get($donation, 'source_label') }}</div>
                            <div class="mt-1 text-xs uppercase tracking-[0.16em] text-slate-500">{{ data_get($donation, 'display_label') }}</div>
                        </td>
                        <td>
                            <div>{{ data_get($donation, 'reference') }}</div>
                            <div class="mt-1 text-xs text-slate-500">{{ data_get($donation, 'account_name') }}</div>
                        </td>
                        <td>{{ data_get($donation, 'note') ?: '-' }}</td>
                        <td data-numeric="true">{{ $money(data_get($donation, 'amount')) }} {{ data_get($donation, 'currency', 'BDT') }}</td>
                        <td>{{ optional(data_get($donation, 'occurred_at'))->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-slate-500">
                            No donation history has been recorded for this donor profile yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <x-slot name="mobile">
                @forelse ($donations as $donation)
                    <x-ui.card soft>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm font-semibold text-slate-950">{{ data_get($donation, 'title') }}</div>
                                <x-ui.badge variant="info">{{ data_get($donation, 'source_label') }}</x-ui.badge>
                            </div>
                            <p class="text-sm leading-6 text-slate-600">{{ data_get($donation, 'note') ?: 'No additional note recorded.' }}</p>
                            <dl class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <dt class="ui-stat-label">Reference</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ data_get($donation, 'reference') }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Display</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ data_get($donation, 'display_label') }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Amount</dt>
                                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $money(data_get($donation, 'amount')) }} {{ data_get($donation, 'currency', 'BDT') }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Date</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ optional(data_get($donation, 'occurred_at'))->format('Y-m-d H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </x-ui.card>
                @empty
                    <x-ui.empty-state
                        title="No donation history yet"
                        description="No donation history has been recorded for this donor profile yet."
                    />
                @endforelse
            </x-slot>
        </x-ui.table>

        @if ($donations->hasPages())
            <div class="mt-4">
                {{ $donations->links() }}
            </div>
        @endif
    </div>
</x-donor-layout>
