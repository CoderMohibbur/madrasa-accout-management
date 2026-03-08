@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
@endphp

<x-guardian-layout
    :title="$invoice->invoice_number"
    description="Invoice details stay read-only in Phase 2. Payment initiation and gateway flows remain blocked until the later payment phase."
>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Student</div>
            <div class="mt-3 text-xl font-semibold text-white">{{ $invoice->student?->full_name ?: 'Student #'.$invoice->student_id }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Status</div>
            <div class="mt-3 text-xl font-semibold text-white">{{ strtoupper($invoice->status) }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Total Amount</div>
            <div class="mt-3 text-xl font-semibold text-white">{{ $money($invoice->total_amount) }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Balance</div>
            <div class="mt-3 text-xl font-semibold text-white">{{ $money($invoice->balance_amount) }}</div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[0.95fr,1.05fr]">
        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
            <h2 class="text-lg font-semibold text-white">Invoice Summary</h2>
            <dl class="mt-5 space-y-4 text-sm text-slate-300">
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Issued Date</dt>
                    <dd class="mt-1">{{ $invoice->issued_at?->format('Y-m-d') ?: 'Not scheduled' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Due Date</dt>
                    <dd class="mt-1">{{ $invoice->due_at?->format('Y-m-d') ?: 'Not scheduled' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Paid Amount</dt>
                    <dd class="mt-1">{{ $money($invoice->paid_amount) }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Notes</dt>
                    <dd class="mt-1 leading-6">{{ $invoice->notes ?: 'No invoice note was recorded.' }}</dd>
                </div>
            </dl>
        </section>

        <section class="overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70">
            <div class="border-b border-white/10 px-6 py-5">
                <h2 class="text-lg font-semibold text-white">Invoice Items</h2>
                <p class="mt-1 text-sm text-slate-400">The line items below are read from the dedicated student billing tables added in Phase 1.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-sm">
                    <thead class="bg-white/5 text-left text-xs uppercase tracking-[0.2em] text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Title</th>
                            <th class="px-6 py-4">Description</th>
                            <th class="px-6 py-4">Quantity</th>
                            <th class="px-6 py-4">Unit Amount</th>
                            <th class="px-6 py-4">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($invoice->items as $item)
                            <tr class="text-slate-300">
                                <td class="px-6 py-4 font-medium text-white">{{ $item->title }}</td>
                                <td class="px-6 py-4">{{ $item->description ?: '-' }}</td>
                                <td class="px-6 py-4">{{ $item->quantity }}</td>
                                <td class="px-6 py-4">{{ $money($item->unit_amount) }}</td>
                                <td class="px-6 py-4">{{ $money($item->line_total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-400">
                                    No invoice items have been attached yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <section class="mt-6 overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70">
        <div class="border-b border-white/10 px-6 py-5">
            <h2 class="text-lg font-semibold text-white">Payment and Receipt History</h2>
            <p class="mt-1 text-sm text-slate-400">This is visibility only. Phase 2 does not introduce payment initiation or gateway callbacks.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10 text-sm">
                <thead class="bg-white/5 text-left text-xs uppercase tracking-[0.2em] text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Provider</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Receipt</th>
                        <th class="px-6 py-4">Reference</th>
                        <th class="px-6 py-4">Recorded</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($invoice->payments as $payment)
                        <tr class="text-slate-300">
                            <td class="px-6 py-4">{{ strtoupper($payment->status) }}</td>
                            <td class="px-6 py-4">{{ $payment->provider ?: 'manual' }}</td>
                            <td class="px-6 py-4">{{ $money($payment->amount) }}</td>
                            <td class="px-6 py-4">{{ $payment->receipt?->receipt_number ?: 'Receipt pending' }}</td>
                            <td class="px-6 py-4">{{ $payment->provider_reference ?: $payment->idempotency_key }}</td>
                            <td class="px-6 py-4">
                                {{ optional($payment->paid_at ?? $payment->initiated_at ?? $payment->created_at)->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-400">
                                No payment rows have been attached to this invoice yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-guardian-layout>
