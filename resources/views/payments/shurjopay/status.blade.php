<x-guardian-layout :title="$title" :description="$description">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Status</div>
            <div class="mt-3 text-2xl font-semibold text-white">{{ strtoupper($result->status) }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Provider</div>
            <div class="mt-3 text-2xl font-semibold text-white">{{ strtoupper($payment?->provider ?: 'shurjopay') }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Amount</div>
            <div class="mt-3 text-2xl font-semibold text-white">
                {{ number_format((float) ($payment?->amount ?? 0), 2) }} {{ $payment?->currency ?: config('payments.default_currency', 'BDT') }}
            </div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Reference</div>
            <div class="mt-3 text-sm font-semibold text-white">
                {{ $payment?->provider_reference ?: $payment?->idempotency_key ?: 'Unavailable' }}
            </div>
        </div>
    </div>

    <section class="mt-6 rounded-[2rem] border border-white/10 bg-slate-900/70 p-6">
        <h2 class="text-lg font-semibold text-white">Outcome</h2>
        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">{{ $result->message }}</p>

        @if ($payment)
            <dl class="mt-6 grid gap-4 text-sm text-slate-300 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Verification Status</dt>
                    <dd class="mt-2 font-semibold text-white">{{ strtoupper($payment->verification_status ?: 'pending') }}</dd>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Receipt</dt>
                    <dd class="mt-2 font-semibold text-white">{{ $payment->receipt?->receipt_number ?: 'Not issued' }}</dd>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Invoice</dt>
                    <dd class="mt-2 font-semibold text-white">{{ $invoice?->invoice_number ?: 'Not linked' }}</dd>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Status Reason</dt>
                    <dd class="mt-2 leading-6">{{ $payment->status_reason ?: 'No extra detail was recorded.' }}</dd>
                </div>
            </dl>
        @endif

        <div class="mt-6 flex flex-wrap gap-3">
            @if ($invoice)
                <a href="{{ route('guardian.invoices.show', $invoice) }}"
                    class="inline-flex items-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300">
                    Back To Invoice
                </a>
            @endif

            <a href="{{ route('guardian.history.index') }}"
                class="inline-flex items-center rounded-full border border-white/10 px-5 py-3 text-sm font-semibold text-slate-100 transition hover:bg-white/5">
                Payment History
            </a>
        </div>
    </section>
</x-guardian-layout>
