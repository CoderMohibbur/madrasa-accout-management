@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
@endphp

<x-guardian-layout
    title="Payment History"
    description="This page shows guardian-visible payment rows and their receipt numbers without enabling any new payment write flow."
>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card label="Payment rows" value="{{ $summary['payment_count'] }}" meta="Total guardian-visible payment attempts." />
        <x-ui.stat-card label="Paid rows" value="{{ $summary['paid_count'] }}" meta="Rows currently marked paid in the guardian scope." />
        <x-ui.stat-card label="Receipts" value="{{ $summary['receipt_count'] }}" meta="Issued receipts visible from linked guardian records." />
        <x-ui.stat-card label="Verified paid total" value="{{ $money($summary['paid_total']) }}" meta="Verified payment total across guardian-visible rows." />
    </div>

    <div class="mt-6">
        <x-ui.table
            title="Guardian payment history"
            description="Guardian-visible payment rows remain scoped to linked student invoices only."
        >
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Student</th>
                    <th>Provider</th>
                    <th>Status</th>
                    <th data-numeric="true">Amount</th>
                    <th>Receipt</th>
                    <th>Reference</th>
                    <th>Recorded</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>
                            <a href="{{ route('guardian.invoices.show', $payment->payable) }}" class="font-semibold text-emerald-700 hover:text-emerald-800">
                                {{ $payment->payable?->invoice_number ?: 'Payment #'.$payment->id }}
                            </a>
                        </td>
                        <td>{{ $payment->payable?->student?->full_name ?: 'Linked student' }}</td>
                        <td>{{ $payment->provider ?: 'manual' }}</td>
                        <td>{{ strtoupper($payment->status) }}</td>
                        <td data-numeric="true">{{ $money($payment->amount) }}</td>
                        <td>{{ $payment->receipt?->receipt_number ?: 'Receipt pending' }}</td>
                        <td>{{ $payment->provider_reference ?: $payment->idempotency_key }}</td>
                        <td>{{ optional($payment->paid_at ?? $payment->initiated_at ?? $payment->created_at)->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-slate-500">
                            No guardian-visible payment history exists yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <x-slot name="mobile">
                @forelse ($payments as $payment)
                    <x-ui.card soft>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <a href="{{ route('guardian.invoices.show', $payment->payable) }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                                    {{ $payment->payable?->invoice_number ?: 'Payment #'.$payment->id }}
                                </a>
                                <x-ui.badge variant="info">{{ strtoupper($payment->status) }}</x-ui.badge>
                            </div>
                            <dl class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <dt class="ui-stat-label">Student</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ $payment->payable?->student?->full_name ?: 'Linked student' }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Provider</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ $payment->provider ?: 'manual' }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Amount</dt>
                                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $money($payment->amount) }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Receipt</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ $payment->receipt?->receipt_number ?: 'Receipt pending' }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="ui-stat-label">Reference</dt>
                                    <dd class="mt-2 break-all text-sm text-slate-700">{{ $payment->provider_reference ?: $payment->idempotency_key }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="ui-stat-label">Recorded</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ optional($payment->paid_at ?? $payment->initiated_at ?? $payment->created_at)->format('Y-m-d H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </x-ui.card>
                @empty
                    <x-ui.empty-state
                        title="No payment history yet"
                        description="No guardian-visible payment history exists yet."
                    />
                @endforelse
            </x-slot>
        </x-ui.table>

        @if ($payments->hasPages())
            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</x-guardian-layout>
