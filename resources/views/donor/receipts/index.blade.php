@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
@endphp

<x-donor-layout
    title="Receipt History"
    description="Only donor-scoped receipts appear here. New online donor receipts and legacy donor receipts stay visible together without broadening guest access."
>
    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.stat-card label="Receipts" value="{{ $summary['receipt_count'] }}" meta="All donor-visible receipts linked to this account scope." />
        <x-ui.stat-card label="Total receipted" value="{{ $money($summary['receipt_total']) }}" meta="Receipt total across legacy and online donor records." />
        <x-ui.stat-card label="Latest receipt" value="{{ $summary['latest_receipt_at'] ? $summary['latest_receipt_at']->format('Y-m-d H:i') : '-' }}" meta="Most recent donor-visible receipt issue time." />
    </div>

    <div class="mt-6">
        <x-ui.table
            title="Receipt history"
            description="Legacy and online donor receipts stay visible together while transaction-specific guest access remains separate."
        >
            <thead>
                <tr>
                    <th>Source</th>
                    <th>Receipt</th>
                    <th>Provider</th>
                    <th>Status</th>
                    <th data-numeric="true">Amount</th>
                    <th>Reference</th>
                    <th>Issued</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($receipts as $receipt)
                    <tr>
                        <td>{{ data_get($receipt, 'source_label') }}</td>
                        <td class="font-semibold text-slate-950">{{ data_get($receipt, 'receipt_number') }}</td>
                        <td>{{ data_get($receipt, 'provider') }}</td>
                        <td>{{ data_get($receipt, 'status_label') }}</td>
                        <td data-numeric="true">{{ $money(data_get($receipt, 'amount')) }} {{ data_get($receipt, 'currency', 'BDT') }}</td>
                        <td>{{ data_get($receipt, 'reference') }}</td>
                        <td>{{ optional(data_get($receipt, 'issued_at'))->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-slate-500">
                            No donor-visible receipts are available yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <x-slot name="mobile">
                @forelse ($receipts as $receipt)
                    <x-ui.card soft>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm font-semibold text-slate-950">{{ data_get($receipt, 'receipt_number') }}</div>
                                <x-ui.badge variant="warning">{{ data_get($receipt, 'source_label') }}</x-ui.badge>
                            </div>
                            <dl class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <dt class="ui-stat-label">Provider</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ data_get($receipt, 'provider') }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Status</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ data_get($receipt, 'status_label') }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Amount</dt>
                                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $money(data_get($receipt, 'amount')) }} {{ data_get($receipt, 'currency', 'BDT') }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Issued</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ optional(data_get($receipt, 'issued_at'))->format('Y-m-d H:i') }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="ui-stat-label">Reference</dt>
                                    <dd class="mt-2 break-all text-sm text-slate-700">{{ data_get($receipt, 'reference') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </x-ui.card>
                @empty
                    <x-ui.empty-state
                        title="No donor-visible receipts yet"
                        description="No donor-visible receipts are available yet."
                    />
                @endforelse
            </x-slot>
        </x-ui.table>

        @if ($receipts->hasPages())
            <div class="mt-4">
                {{ $receipts->links() }}
            </div>
        @endif
    </div>
</x-donor-layout>
