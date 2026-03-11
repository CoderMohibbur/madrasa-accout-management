@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
@endphp

<x-guardian-layout
    title="Guardian Invoices"
    description="Every invoice here is filtered through the guardian-student links and the Phase 1 invoice ownership policy."
>
    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.stat-card label="Visible invoices" value="{{ $summary['invoice_count'] }}" meta="Invoices currently visible through guardian-safe ownership rules." />
        <x-ui.stat-card label="Overdue" value="{{ $summary['overdue_count'] }}" meta="Guardian-visible overdue invoices only." />
        <x-ui.stat-card label="Open balance" value="{{ $money($summary['total_balance']) }}" meta="Total open balance across visible linked invoices." />
    </div>

    <div class="mt-6">
        <x-ui.table
            title="Guardian invoices"
            description="Every row here remains filtered by guardian linkage plus the invoice ownership policy."
        >
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Student</th>
                    <th>Issued</th>
                    <th>Due</th>
                    <th data-numeric="true">Total</th>
                    <th data-numeric="true">Balance</th>
                    <th data-numeric="true">Items</th>
                    <th data-numeric="true">Payments</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                    <tr>
                        <td>
                            <a href="{{ route('guardian.invoices.show', $invoice) }}" class="font-semibold text-emerald-700 hover:text-emerald-800">
                                {{ $invoice->invoice_number }}
                            </a>
                            <div class="mt-1 text-xs uppercase tracking-[0.16em] text-slate-500">{{ strtoupper($invoice->status) }}</div>
                        </td>
                        <td>{{ $invoice->student?->full_name ?: 'Student #'.$invoice->student_id }}</td>
                        <td>{{ $invoice->issued_at?->format('Y-m-d') ?: '-' }}</td>
                        <td>{{ $invoice->due_at?->format('Y-m-d') ?: '-' }}</td>
                        <td data-numeric="true">{{ $money($invoice->total_amount) }}</td>
                        <td data-numeric="true">{{ $money($invoice->balance_amount) }}</td>
                        <td data-numeric="true">{{ $invoice->items_count }}</td>
                        <td data-numeric="true">{{ $invoice->payments_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-slate-500">
                            No guardian-visible invoices are available yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <x-slot name="mobile">
                @forelse ($invoices as $invoice)
                    <x-ui.card soft>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <a href="{{ route('guardian.invoices.show', $invoice) }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                                    {{ $invoice->invoice_number }}
                                </a>
                                <x-ui.badge variant="info">{{ strtoupper($invoice->status) }}</x-ui.badge>
                            </div>
                            <dl class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <dt class="ui-stat-label">Student</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ $invoice->student?->full_name ?: 'Student #'.$invoice->student_id }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Due</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ $invoice->due_at?->format('Y-m-d') ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Total</dt>
                                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $money($invoice->total_amount) }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Balance</dt>
                                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $money($invoice->balance_amount) }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Items</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ $invoice->items_count }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Payments</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ $invoice->payments_count }}</dd>
                                </div>
                            </dl>
                        </div>
                    </x-ui.card>
                @empty
                    <x-ui.empty-state
                        title="No invoices yet"
                        description="No guardian-visible invoices are available yet."
                    />
                @endforelse
            </x-slot>
        </x-ui.table>

        @if ($invoices->hasPages())
            <div class="mt-4">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</x-guardian-layout>
