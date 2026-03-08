@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
    $activeAttemptStatuses = [
        \App\Models\Payment::STATUS_PENDING,
        \App\Models\Payment::STATUS_REDIRECT_PENDING,
        \App\Models\Payment::STATUS_PENDING_VERIFICATION,
        \App\Models\Payment::STATUS_AWAITING_MANUAL_PAYMENT,
        \App\Models\Payment::STATUS_MANUAL_REVIEW,
    ];
    $sortedPayments = $invoice->payments->sortByDesc('id');
    $activeAttempt = $sortedPayments->first(fn ($payment) => in_array($payment->status, $activeAttemptStatuses, true));
@endphp

<x-guardian-layout
    :title="$invoice->invoice_number"
    description="Sandbox-only payment options are now available for invoice-backed guardian payments. Live WordPress IPN routing remains untouched."
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

    @if ($errors->any())
        <div class="mt-6 rounded-3xl border border-rose-400/20 bg-rose-400/10 px-5 py-4 text-sm text-rose-100">
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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

    <section class="mt-6 rounded-[2rem] border border-white/10 bg-slate-900/70 p-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-white">Sandbox Payment Options</h2>
                <p class="mt-1 text-sm text-slate-400">
                    Laravel can initiate sandbox shurjoPay or collect manual-bank evidence here. Live merchant routing and the existing WordPress IPN remain unchanged.
                </p>
            </div>
            <div class="rounded-full border border-amber-400/20 bg-amber-400/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-amber-100">
                Sandbox Only
            </div>
        </div>

        @if ((float) $invoice->balance_amount <= 0)
            <div class="mt-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-sm text-emerald-100">
                This invoice is already fully settled. No new payment attempt is needed.
            </div>
        @elseif ($activeAttempt)
            <div class="mt-6 rounded-2xl border border-sky-400/20 bg-sky-400/10 p-4 text-sm text-sky-100">
                An active payment attempt already exists for this invoice: <strong>{{ strtoupper($activeAttempt->provider ?: 'manual_bank') }}</strong>
                with status <strong>{{ strtoupper($activeAttempt->status) }}</strong>.
            </div>

            <div class="mt-4 flex flex-wrap gap-3">
                @if ($activeAttempt->provider === 'shurjopay' && data_get($activeAttempt->metadata, 'shurjopay.initiate_response.checkout_url'))
                    <a href="{{ data_get($activeAttempt->metadata, 'shurjopay.initiate_response.checkout_url') }}"
                        class="inline-flex items-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300">
                        Resume shurjoPay Checkout
                    </a>
                @endif

                @if ($activeAttempt->provider === 'manual_bank')
                    <a href="{{ route('payments.manual-bank.show', $activeAttempt) }}"
                        class="inline-flex items-center rounded-full border border-white/10 px-5 py-3 text-sm font-semibold text-slate-100 transition hover:bg-white/5">
                        Review Manual Bank Submission
                    </a>
                @endif
            </div>
        @else
            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <section class="rounded-[1.75rem] border border-white/10 bg-white/5 p-5">
                    <h3 class="text-base font-semibold text-white">shurjoPay Sandbox</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-300">
                        Starts a sandbox checkout only. Laravel finalizes nothing until server-side verification confirms the result.
                    </p>

                    <form method="POST" action="{{ route('payments.shurjopay.initiate') }}" class="mt-5">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                        <button type="submit"
                            class="inline-flex items-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300">
                            Pay {{ $money($invoice->balance_amount) }} via shurjoPay
                        </button>
                    </form>
                </section>

                <section class="rounded-[1.75rem] border border-white/10 bg-white/5 p-5">
                    <h3 class="text-base font-semibold text-white">Manual Bank Review</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-300">
                        Submit transfer evidence for management approval. No receipt is issued until the transfer is manually approved.
                    </p>

                    <form method="POST" action="{{ route('payments.manual-bank.requests.store') }}" class="mt-5 space-y-4">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                        <div>
                            <label for="payer_name" class="text-xs uppercase tracking-[0.2em] text-slate-500">Payer Name</label>
                            <input id="payer_name" name="payer_name" type="text" value="{{ old('payer_name', auth()->user()->name) }}"
                                class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-emerald-400 focus:outline-none">
                        </div>

                        <div>
                            <label for="bank_reference" class="text-xs uppercase tracking-[0.2em] text-slate-500">Bank Reference</label>
                            <input id="bank_reference" name="bank_reference" type="text" value="{{ old('bank_reference') }}"
                                class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-emerald-400 focus:outline-none">
                        </div>

                        <div>
                            <label for="transferred_at" class="text-xs uppercase tracking-[0.2em] text-slate-500">Transferred At</label>
                            <input id="transferred_at" name="transferred_at" type="datetime-local" value="{{ old('transferred_at') }}"
                                class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-emerald-400 focus:outline-none">
                        </div>

                        <div>
                            <label for="note" class="text-xs uppercase tracking-[0.2em] text-slate-500">Note</label>
                            <textarea id="note" name="note" rows="3"
                                class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-emerald-400 focus:outline-none">{{ old('note') }}</textarea>
                        </div>

                        <button type="submit"
                            class="inline-flex items-center rounded-full border border-white/10 px-5 py-3 text-sm font-semibold text-slate-100 transition hover:bg-white/5">
                            Submit Manual Bank Evidence
                        </button>
                    </form>
                </section>
            </div>
        @endif
    </section>

    <section class="mt-6 overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70">
        <div class="border-b border-white/10 px-6 py-5">
            <h2 class="text-lg font-semibold text-white">Payment and Receipt History</h2>
            <p class="mt-1 text-sm text-slate-400">Sandbox payment attempts and manual-bank review rows appear here alongside any issued receipts.</p>
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
