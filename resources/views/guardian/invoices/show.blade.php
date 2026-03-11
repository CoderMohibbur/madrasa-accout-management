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
        <x-ui.stat-card label="Student" value="{{ $invoice->student?->full_name ?: 'Student #'.$invoice->student_id }}" meta="Linked guardian-visible student only." />
        <x-ui.stat-card label="Status" value="{{ strtoupper($invoice->status) }}" meta="Current invoice state in the guardian portal." />
        <x-ui.stat-card label="Total amount" value="{{ $money($invoice->total_amount) }}" meta="Total billed amount for this invoice." />
        <x-ui.stat-card label="Balance" value="{{ $money($invoice->balance_amount) }}" meta="Open balance still eligible for guardian payment attempts." />
    </div>

    @if ($errors->any())
        <div class="mt-6">
            <x-ui.alert variant="error" title="Payment request could not be submitted">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        </div>
    @endif

    <div class="mt-6 grid gap-6 xl:grid-cols-[0.95fr,1.05fr]">
        <x-ui.card
            title="Invoice summary"
            description="Key dates, balances, and notes for this linked invoice."
        >
            <dl class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Issued date</dt>
                    <dd class="mt-2 text-sm text-slate-700">{{ $invoice->issued_at?->format('Y-m-d') ?: 'Not scheduled' }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Due date</dt>
                    <dd class="mt-2 text-sm text-slate-700">{{ $invoice->due_at?->format('Y-m-d') ?: 'Not scheduled' }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Paid amount</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $money($invoice->paid_amount) }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Currency</dt>
                    <dd class="mt-2 text-sm text-slate-700">{{ $invoice->currency ?: 'BDT' }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3 sm:col-span-2">
                    <dt class="ui-stat-label">Notes</dt>
                    <dd class="mt-2 text-sm leading-6 text-slate-700">{{ $invoice->notes ?: 'No invoice note was recorded.' }}</dd>
                </div>
            </dl>
        </x-ui.card>

        <x-ui.table
            title="Invoice items"
            description="The line items below are read from the dedicated student billing tables added in Phase 1."
        >
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th data-numeric="true">Quantity</th>
                    <th data-numeric="true">Unit amount</th>
                    <th data-numeric="true">Line total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoice->items as $item)
                    <tr>
                        <td class="font-medium text-slate-950">{{ $item->title }}</td>
                        <td>{{ $item->description ?: '-' }}</td>
                        <td data-numeric="true">{{ $item->quantity }}</td>
                        <td data-numeric="true">{{ $money($item->unit_amount) }}</td>
                        <td data-numeric="true">{{ $money($item->line_total) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-slate-500">
                            No invoice items have been attached yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <x-slot name="mobile">
                @forelse ($invoice->items as $item)
                    <x-ui.card soft>
                        <div class="space-y-3">
                            <div class="text-sm font-semibold text-slate-950">{{ $item->title }}</div>
                            <p class="text-sm leading-6 text-slate-600">{{ $item->description ?: 'No line description recorded.' }}</p>
                            <dl class="grid gap-3 sm:grid-cols-3">
                                <div>
                                    <dt class="ui-stat-label">Qty</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ $item->quantity }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Unit</dt>
                                    <dd class="mt-2 text-sm text-slate-700">{{ $money($item->unit_amount) }}</dd>
                                </div>
                                <div>
                                    <dt class="ui-stat-label">Total</dt>
                                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $money($item->line_total) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </x-ui.card>
                @empty
                    <x-ui.empty-state
                        title="No invoice items yet"
                        description="No invoice items have been attached yet."
                    />
                @endforelse
            </x-slot>
        </x-ui.table>
    </div>

    <div class="mt-6">
        <x-ui.card
            title="Sandbox payment options"
            description="Laravel can initiate sandbox shurjoPay or collect manual-bank evidence here. Live merchant routing and the existing WordPress IPN remain unchanged."
        >
            <x-slot name="headerActions">
                <x-ui.badge variant="warning">Sandbox only</x-ui.badge>
            </x-slot>

            @if ((float) $invoice->balance_amount <= 0)
                <x-ui.alert variant="success" title="Invoice already settled">
                    This invoice is already fully settled. No new payment attempt is needed.
                </x-ui.alert>
            @elseif ($activeAttempt)
                <x-ui.alert variant="info" title="Active payment attempt found">
                    An active payment attempt already exists for this invoice:
                    <strong>{{ strtoupper($activeAttempt->provider ?: 'manual_bank') }}</strong>
                    with status <strong>{{ strtoupper($activeAttempt->status) }}</strong>.
                </x-ui.alert>

                <div class="mt-4 flex flex-wrap gap-3">
                    @if ($activeAttempt->provider === 'shurjopay' && data_get($activeAttempt->metadata, 'shurjopay.initiate_response.checkout_url'))
                        <a
                            href="{{ data_get($activeAttempt->metadata, 'shurjopay.initiate_response.checkout_url') }}"
                            class="ui-button ui-button--primary"
                        >
                            Resume shurjoPay checkout
                        </a>
                    @endif

                    @if ($activeAttempt->provider === 'manual_bank')
                        <a href="{{ route('payments.manual-bank.show', $activeAttempt) }}" class="ui-button ui-button--secondary">
                            Review manual bank submission
                        </a>
                    @endif
                </div>
            @else
                <div class="grid gap-6 lg:grid-cols-2">
                    <x-ui.card
                        title="shurjoPay sandbox"
                        description="Starts a sandbox checkout only. Laravel finalizes nothing until server-side verification confirms the result."
                        soft
                    >
                        <form method="POST" action="{{ route('payments.shurjopay.initiate') }}">
                            @csrf
                            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                            <button type="submit" class="ui-button ui-button--primary">
                                Pay {{ $money($invoice->balance_amount) }} via shurjoPay
                            </button>
                        </form>
                    </x-ui.card>

                    <x-ui.card
                        title="Manual bank review"
                        description="Submit transfer evidence for management approval. No receipt is issued until the transfer is manually approved."
                        soft
                    >
                        <form method="POST" action="{{ route('payments.manual-bank.requests.store') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                            <div>
                                <x-input-label for="payer_name" :value="__('Payer Name')" />
                                <x-text-input
                                    id="payer_name"
                                    name="payer_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('payer_name', auth()->user()->name)"
                                />
                            </div>

                            <div>
                                <x-input-label for="bank_reference" :value="__('Bank Reference')" />
                                <x-text-input
                                    id="bank_reference"
                                    name="bank_reference"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('bank_reference')"
                                />
                            </div>

                            <div>
                                <x-input-label for="transferred_at" :value="__('Transferred At')" />
                                <x-text-input
                                    id="transferred_at"
                                    name="transferred_at"
                                    type="datetime-local"
                                    class="mt-1 block w-full"
                                    :value="old('transferred_at')"
                                />
                            </div>

                            <div>
                                <x-input-label for="note" :value="__('Note')" />
                                <textarea id="note" name="note" rows="3" class="ui-textarea mt-1">{{ old('note') }}</textarea>
                            </div>

                            <button type="submit" class="ui-button ui-button--secondary">
                                Submit manual bank evidence
                            </button>
                        </form>
                    </x-ui.card>
                </div>
            @endif
        </x-ui.card>
    </div>

    <div class="mt-6">
        <x-ui.table
            title="Payment and receipt history"
            description="Sandbox payment attempts and manual-bank review rows appear here alongside any issued receipts."
        >
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Provider</th>
                    <th data-numeric="true">Amount</th>
                    <th>Receipt</th>
                    <th>Reference</th>
                    <th>Recorded</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoice->payments as $payment)
                    <tr>
                        <td>{{ strtoupper($payment->status) }}</td>
                        <td>{{ $payment->provider ?: 'manual' }}</td>
                        <td data-numeric="true">{{ $money($payment->amount) }}</td>
                        <td>{{ $payment->receipt?->receipt_number ?: 'Receipt pending' }}</td>
                        <td>{{ $payment->provider_reference ?: $payment->idempotency_key }}</td>
                        <td>{{ optional($payment->paid_at ?? $payment->initiated_at ?? $payment->created_at)->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-slate-500">
                            No payment rows have been attached to this invoice yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <x-slot name="mobile">
                @forelse ($invoice->payments as $payment)
                    <x-ui.card soft>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm font-semibold text-slate-950">{{ strtoupper($payment->status) }}</div>
                                <x-ui.badge variant="info">{{ $payment->provider ?: 'manual' }}</x-ui.badge>
                            </div>
                            <dl class="grid gap-3 sm:grid-cols-2">
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
                        title="No payment rows yet"
                        description="No payment rows have been attached to this invoice yet."
                    />
                @endforelse
            </x-slot>
        </x-ui.table>
    </div>
</x-guardian-layout>
