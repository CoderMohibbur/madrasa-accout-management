@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
@endphp

<x-guardian-layout
    title="Guardian Dashboard"
    description="Review linked students, open invoices, and recent payment activity without touching the legacy management screens."
>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card label="Linked students" value="{{ $summary['student_count'] }}" meta="Only guardian-linked students appear in this portal." />
        <x-ui.stat-card label="Open invoices" value="{{ $summary['open_invoice_count'] }}" meta="Current guardian-visible open billing rows." />
        <x-ui.stat-card label="Outstanding balance" value="{{ $money($summary['outstanding_balance']) }}" meta="Open balance across visible linked invoices." />
        <x-ui.stat-card label="Verified paid total" value="{{ $money($summary['paid_total']) }}" meta="Verified guardian-visible paid total only." />
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-[1.2fr,0.8fr]">
        <x-ui.card
            title="Linked students"
            description="Only students attached to this guardian profile are shown here."
        >
            @if ($students->isEmpty())
                <x-ui.empty-state
                    title="No linked students yet"
                    description="No linked students are available for this guardian profile yet."
                />
            @else
                <div class="ui-list-divider">
                    @foreach ($students as $student)
                        @php
                            $yearLabel = data_get($student, 'academicYear.year') ?? data_get($student, 'academicYear.name') ?? 'Not assigned';
                            $classLabel = data_get($student, 'class.name') ?? data_get($student, 'class.class_name') ?? 'No class';
                            $sectionLabel = data_get($student, 'section.name') ?? data_get($student, 'section.section_name') ?? 'No section';
                        @endphp

                        <a href="{{ route('guardian.students.show', $student) }}" class="ui-list-row">
                            <div>
                                <div class="text-lg font-semibold text-slate-950">{{ $student->full_name ?: 'Student #'.$student->id }}</div>
                                <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-500">
                                    <span class="ui-pill">Roll {{ $student->roll ?: '-' }}</span>
                                    <span class="ui-pill">{{ $yearLabel }}</span>
                                    <span class="ui-pill">{{ $classLabel }} / {{ $sectionLabel }}</span>
                                    <span class="ui-pill">{{ $student->pivot->relationship_label ?: 'Linked guardian' }}</span>
                                </div>
                            </div>

                            <div class="text-sm font-medium text-emerald-700">
                                View profile
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-ui.card>

        <x-ui.card
            title="Guardian profile"
            description="Profile details remain informational and separate from management editing in this rollout."
            soft
        >
            <dl class="grid gap-4">
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-3">
                    <dt class="ui-stat-label">Name</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $guardian->name ?: Auth::user()->name }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-3">
                    <dt class="ui-stat-label">Email</dt>
                    <dd class="mt-2 text-sm text-slate-700">{{ $guardian->email ?: Auth::user()->email }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-3">
                    <dt class="ui-stat-label">Mobile</dt>
                    <dd class="mt-2 text-sm text-slate-700">{{ $guardian->mobile ?: 'Not provided' }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-3">
                    <dt class="ui-stat-label">Address</dt>
                    <dd class="mt-2 text-sm leading-6 text-slate-700">{{ $guardian->address ?: 'No address has been added for this portal profile yet.' }}</dd>
                </div>
            </dl>
        </x-ui.card>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        <x-ui.card
            title="Recent invoices"
            description="Latest fee records linked to the guardian profile."
        >
            <x-slot name="headerActions">
                <a href="{{ route('guardian.invoices.index') }}" class="ui-button ui-button--secondary">View all</a>
            </x-slot>

            @if ($recentInvoices->isEmpty())
                <x-ui.empty-state
                    title="No guardian-visible invoices yet"
                    description="No invoices have been issued through the new Phase 1 billing model yet."
                />
            @else
                <div class="ui-list-divider">
                    @foreach ($recentInvoices as $invoice)
                        <a href="{{ route('guardian.invoices.show', $invoice) }}" class="ui-list-row">
                            <div>
                                <div class="font-semibold text-slate-950">{{ $invoice->invoice_number }}</div>
                                <div class="mt-1 text-sm text-slate-600">
                                    {{ $invoice->student?->full_name ?: 'Student #'.$invoice->student_id }}
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

        <x-ui.card
            title="Recent payment activity"
            description="Receipt visibility stays scoped to linked student invoices."
        >
            <x-slot name="headerActions">
                <a href="{{ route('guardian.history.index') }}" class="ui-button ui-button--secondary">History</a>
            </x-slot>

            @if ($recentPayments->isEmpty())
                <x-ui.empty-state
                    title="No payment history yet"
                    description="No payment history is available for the linked invoices yet."
                />
            @else
                <div class="ui-list-divider">
                    @foreach ($recentPayments as $payment)
                        <div class="ui-list-row">
                            <div>
                                <div class="font-semibold text-slate-950">
                                    {{ $payment->payable?->invoice_number ?: 'Payment #'.$payment->id }}
                                </div>
                                <div class="mt-1 text-sm text-slate-600">
                                    {{ $payment->payable?->student?->full_name ?: 'Linked student' }}
                                </div>
                            </div>
                            <div class="text-sm text-slate-700">
                                <div class="font-semibold text-slate-950">{{ strtoupper($payment->status) }} - {{ $money($payment->amount) }}</div>
                                <div class="mt-1 text-slate-500">
                                    {{ $payment->receipt?->receipt_number ?: 'Receipt pending' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>
</x-guardian-layout>
