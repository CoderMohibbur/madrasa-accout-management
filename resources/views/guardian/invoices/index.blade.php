@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
@endphp

<x-guardian-layout
    title="Guardian Invoices"
    description="Every invoice here is filtered through the guardian-student links and the Phase 1 invoice ownership policy."
>
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Visible Invoices</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $summary['invoice_count'] }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Overdue</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $summary['overdue_count'] }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Open Balance</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $money($summary['total_balance']) }}</div>
        </div>
    </div>

    <section class="mt-6 overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10 text-sm">
                <thead class="bg-white/5 text-left text-xs uppercase tracking-[0.2em] text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Invoice</th>
                        <th class="px-6 py-4">Student</th>
                        <th class="px-6 py-4">Issued</th>
                        <th class="px-6 py-4">Due</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Balance</th>
                        <th class="px-6 py-4">Items</th>
                        <th class="px-6 py-4">Payments</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($invoices as $invoice)
                        <tr class="text-slate-300">
                            <td class="px-6 py-4">
                                <a href="{{ route('guardian.invoices.show', $invoice) }}" class="font-semibold text-white hover:text-emerald-300">
                                    {{ $invoice->invoice_number }}
                                </a>
                                <div class="mt-1 text-xs text-slate-500">{{ strtoupper($invoice->status) }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $invoice->student?->full_name ?: 'Student #'.$invoice->student_id }}</td>
                            <td class="px-6 py-4">{{ $invoice->issued_at?->format('Y-m-d') ?: '-' }}</td>
                            <td class="px-6 py-4">{{ $invoice->due_at?->format('Y-m-d') ?: '-' }}</td>
                            <td class="px-6 py-4">{{ $money($invoice->total_amount) }}</td>
                            <td class="px-6 py-4">{{ $money($invoice->balance_amount) }}</td>
                            <td class="px-6 py-4">{{ $invoice->items_count }}</td>
                            <td class="px-6 py-4">{{ $invoice->payments_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-slate-400">
                                No guardian-visible invoices are available yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($invoices->hasPages())
            <div class="border-t border-white/10 px-6 py-4">
                {{ $invoices->links() }}
            </div>
        @endif
    </section>
</x-guardian-layout>
