@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
    $yearLabel = data_get($student, 'academicYear.year') ?? data_get($student, 'academicYear.name') ?? 'Not assigned';
    $classLabel = data_get($student, 'class.name') ?? data_get($student, 'class.class_name') ?? 'No class';
    $sectionLabel = data_get($student, 'section.name') ?? data_get($student, 'section.section_name') ?? 'No section';
    $feesTypeLabel = data_get($student, 'feesType.name') ?? data_get($student, 'feesType.title') ?? 'Not assigned';
@endphp

<x-guardian-layout
    :title="$student->full_name ?: 'Student #'.$student->id"
    description="A read-only student view built only from the guardian-safe profile, invoice, and receipt boundaries."
>
    <div class="grid gap-6 xl:grid-cols-[0.95fr,1.05fr]">
        <x-ui.card :title="$student->full_name ?: 'Student #'.$student->id" description="Guardian-visible student context only. Protected scope remains constrained to linked records.">
            <x-slot name="headerActions">
                <x-ui.badge variant="success">{{ $student->pivot->relationship_label ?: 'Guardian link' }}</x-ui.badge>
            </x-slot>

            <dl class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Roll</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $student->roll ?: '-' }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Academic year</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $yearLabel }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Class / section</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $classLabel }} / {{ $sectionLabel }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Fees type</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $feesTypeLabel }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Father name</dt>
                    <dd class="mt-2 text-sm text-slate-700">{{ $student->father_name ?: '-' }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Mobile</dt>
                    <dd class="mt-2 text-sm text-slate-700">{{ $student->mobile ?: '-' }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3 sm:col-span-2">
                    <dt class="ui-stat-label">Address</dt>
                    <dd class="mt-2 text-sm leading-6 text-slate-700">{{ $student->address ?: 'No address has been recorded.' }}</dd>
                </div>
            </dl>
        </x-ui.card>

        <x-ui.card
            title="Invoices"
            description="Only invoices visible through the guardian-safe ownership policy are listed."
        >
            @if ($invoices->isEmpty())
                <x-ui.empty-state
                    title="No linked invoices yet"
                    description="No linked invoices are available for this student yet."
                />
            @else
                <div class="ui-list-divider">
                    @foreach ($invoices as $invoice)
                        <a href="{{ route('guardian.invoices.show', $invoice) }}" class="ui-list-row">
                            <div>
                                <div class="font-semibold text-slate-950">{{ $invoice->invoice_number }}</div>
                                <div class="mt-1 text-sm text-slate-600">
                                    Due {{ $invoice->due_at?->format('Y-m-d') ?: 'Not scheduled' }} - {{ strtoupper($invoice->status) }}
                                </div>
                            </div>
                            <div class="text-sm text-slate-700">
                                <div class="font-semibold text-slate-950">Total {{ $money($invoice->total_amount) }}</div>
                                <div class="mt-1 text-slate-500">Balance {{ $money($invoice->balance_amount) }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>

    <div class="mt-6">
        <x-ui.table
            title="Recent payment history"
            description="Read-only payment and receipt visibility for this linked student."
        >
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Status</th>
                    <th data-numeric="true">Amount</th>
                    <th>Receipt</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ $payment->payable?->invoice_number ?: 'Payment #'.$payment->id }}</td>
                        <td>{{ strtoupper($payment->status) }}</td>
                        <td data-numeric="true">{{ $money($payment->amount) }}</td>
                        <td>{{ $payment->receipt?->receipt_number ?: 'Receipt pending' }}</td>
                        <td>{{ optional($payment->paid_at ?? $payment->initiated_at ?? $payment->created_at)->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-slate-500">
                            No payment activity has been recorded for this student's linked invoices yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <x-slot name="mobile">
                @forelse ($payments as $payment)
                    <x-ui.card soft>
                        <dl class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <dt class="ui-stat-label">Invoice</dt>
                                <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $payment->payable?->invoice_number ?: 'Payment #'.$payment->id }}</dd>
                            </div>
                            <div>
                                <dt class="ui-stat-label">Status</dt>
                                <dd class="mt-2 text-sm text-slate-700">{{ strtoupper($payment->status) }}</dd>
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
                                <dt class="ui-stat-label">Recorded</dt>
                                <dd class="mt-2 text-sm text-slate-700">{{ optional($payment->paid_at ?? $payment->initiated_at ?? $payment->created_at)->format('Y-m-d H:i') }}</dd>
                            </div>
                        </dl>
                    </x-ui.card>
                @empty
                    <x-ui.empty-state
                        title="No payment history yet"
                        description="No payment activity has been recorded for this student's linked invoices yet."
                    />
                @endforelse
            </x-slot>
        </x-ui.table>
    </div>
</x-guardian-layout>
