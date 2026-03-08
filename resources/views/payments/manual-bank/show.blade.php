<x-guardian-layout
    :title="'Manual Bank Payment'"
    description="This sandbox-safe fallback collects bank transfer evidence only. Management approval is still required before Laravel treats the invoice as paid."
>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Invoice</div>
            <div class="mt-3 text-xl font-semibold text-white">{{ $invoice?->invoice_number ?: 'Invoice #'.$payment->payable_id }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Status</div>
            <div class="mt-3 text-xl font-semibold text-white">{{ strtoupper($payment->status) }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Amount</div>
            <div class="mt-3 text-xl font-semibold text-white">{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</div>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Transfer Reference</div>
            <div class="mt-3 text-sm font-semibold text-white">{{ $payment->provider_reference ?: 'Pending' }}</div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[0.95fr,1.05fr]">
        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
            <h2 class="text-lg font-semibold text-white">Submitted Evidence</h2>
            <dl class="mt-5 space-y-4 text-sm text-slate-300">
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Payer Name</dt>
                    <dd class="mt-1">{{ data_get($payment->metadata, 'manual_bank.payer_name', auth()->user()->name) }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Payment Channel</dt>
                    <dd class="mt-1">{{ data_get($payment->metadata, 'manual_bank.payment_channel', config('payments.manual_bank.display_name')) }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Transferred At</dt>
                    <dd class="mt-1">{{ data_get($payment->metadata, 'manual_bank.transferred_at', 'Not supplied') }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Evidence Link</dt>
                    <dd class="mt-1">
                        @if (data_get($payment->metadata, 'manual_bank.evidence_url'))
                            <a href="{{ data_get($payment->metadata, 'manual_bank.evidence_url') }}" class="text-emerald-300 hover:text-emerald-200">
                                {{ data_get($payment->metadata, 'manual_bank.evidence_url') }}
                            </a>
                        @else
                            No external evidence link was supplied.
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Note</dt>
                    <dd class="mt-1 leading-6">{{ data_get($payment->metadata, 'manual_bank.note', 'No extra note was supplied.') }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-6">
            <h2 class="text-lg font-semibold text-white">Bank Instructions</h2>
            <dl class="mt-5 space-y-4 text-sm text-slate-300">
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Display Name</dt>
                    <dd class="mt-1">{{ config('payments.manual_bank.display_name') }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Account Name</dt>
                    <dd class="mt-1">{{ config('payments.manual_bank.account_name') ?: 'Set via environment before sandbox use.' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Bank Name</dt>
                    <dd class="mt-1">{{ config('payments.manual_bank.bank_name') ?: 'Set via environment before sandbox use.' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Branch</dt>
                    <dd class="mt-1">{{ config('payments.manual_bank.branch_name') ?: 'Not configured' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Instructions</dt>
                    <dd class="mt-1 leading-6">{{ config('payments.manual_bank.instructions') }}</dd>
                </div>
            </dl>

            @if ($payment->receipt)
                <div class="mt-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-sm text-emerald-100">
                    Receipt issued: <strong>{{ $payment->receipt->receipt_number }}</strong>
                </div>
            @endif
        </section>
    </div>
</x-guardian-layout>
