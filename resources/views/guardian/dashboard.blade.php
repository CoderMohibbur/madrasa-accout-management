@php
    $money = static fn ($value) => number_format((float) ($value ?? 0), 2);
@endphp

<x-guardian-layout
    title="Guardian Dashboard"
    description="Review linked students, open invoices, and recent payment activity without touching the legacy management screens."
>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Linked Students</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $summary['student_count'] }}</div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Open Invoices</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $summary['open_invoice_count'] }}</div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Outstanding Balance</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $money($summary['outstanding_balance']) }}</div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Verified Paid Total</div>
            <div class="mt-3 text-3xl font-semibold text-white">{{ $money($summary['paid_total']) }}</div>
        </div>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-[1.2fr,0.8fr]">
        <section class="rounded-[2rem] border border-white/10 bg-slate-900/70">
            <div class="flex items-center justify-between border-b border-white/10 px-6 py-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">Linked Students</h2>
                    <p class="mt-1 text-sm text-slate-400">Only students attached to this guardian profile are shown here.</p>
                </div>
            </div>

            <div class="divide-y divide-white/10">
                @forelse ($students as $student)
                    @php
                        $yearLabel = data_get($student, 'academicYear.year') ?? data_get($student, 'academicYear.name') ?? 'Not assigned';
                        $classLabel = data_get($student, 'class.name') ?? data_get($student, 'class.class_name') ?? 'No class';
                        $sectionLabel = data_get($student, 'section.name') ?? data_get($student, 'section.section_name') ?? 'No section';
                    @endphp

                    <a href="{{ route('guardian.students.show', $student) }}" class="block px-6 py-5 transition hover:bg-white/5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <div class="text-lg font-semibold text-white">{{ $student->full_name ?: 'Student #'.$student->id }}</div>
                                <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-400">
                                    <span class="rounded-full border border-white/10 px-3 py-1">Roll {{ $student->roll ?: '-' }}</span>
                                    <span class="rounded-full border border-white/10 px-3 py-1">{{ $yearLabel }}</span>
                                    <span class="rounded-full border border-white/10 px-3 py-1">{{ $classLabel }} / {{ $sectionLabel }}</span>
                                    <span class="rounded-full border border-white/10 px-3 py-1">{{ $student->pivot->relationship_label ?: 'Linked guardian' }}</span>
                                </div>
                            </div>

                            <div class="text-sm text-slate-300">
                                View profile
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-10 text-sm text-slate-400">
                        No linked students are available for this guardian profile yet.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
            <h2 class="text-lg font-semibold text-white">Guardian Profile</h2>
            <dl class="mt-5 space-y-4 text-sm text-slate-300">
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Name</dt>
                    <dd class="mt-1 text-base text-white">{{ $guardian->name ?: Auth::user()->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Email</dt>
                    <dd class="mt-1">{{ $guardian->email ?: Auth::user()->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Mobile</dt>
                    <dd class="mt-1">{{ $guardian->mobile ?: 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Address</dt>
                    <dd class="mt-1 leading-6">{{ $guardian->address ?: 'No address has been added for this portal profile yet.' }}</dd>
                </div>
            </dl>
        </section>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        <section class="rounded-[2rem] border border-white/10 bg-slate-900/70">
            <div class="flex items-center justify-between border-b border-white/10 px-6 py-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">Recent Invoices</h2>
                    <p class="mt-1 text-sm text-slate-400">Latest fee records linked to the guardian profile.</p>
                </div>
                <a href="{{ route('guardian.invoices.index') }}" class="text-sm font-medium text-emerald-300">View all</a>
            </div>

            <div class="divide-y divide-white/10">
                @forelse ($recentInvoices as $invoice)
                    <a href="{{ route('guardian.invoices.show', $invoice) }}" class="block px-6 py-5 transition hover:bg-white/5">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <div class="font-semibold text-white">{{ $invoice->invoice_number }}</div>
                                <div class="mt-1 text-sm text-slate-400">
                                    {{ $invoice->student?->full_name ?: 'Student #'.$invoice->student_id }}
                                </div>
                            </div>
                            <div class="text-sm text-slate-300">
                                <div>Total {{ $money($invoice->total_amount) }}</div>
                                <div class="mt-1 text-slate-400">Balance {{ $money($invoice->balance_amount) }}</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-10 text-sm text-slate-400">
                        No invoices have been issued through the new Phase 1 billing model yet.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="rounded-[2rem] border border-white/10 bg-slate-900/70">
            <div class="flex items-center justify-between border-b border-white/10 px-6 py-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">Recent Payment Activity</h2>
                    <p class="mt-1 text-sm text-slate-400">Receipt visibility stays scoped to linked student invoices.</p>
                </div>
                <a href="{{ route('guardian.history.index') }}" class="text-sm font-medium text-emerald-300">History</a>
            </div>

            <div class="divide-y divide-white/10">
                @forelse ($recentPayments as $payment)
                    <div class="px-6 py-5">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <div class="font-semibold text-white">
                                    {{ $payment->payable?->invoice_number ?: 'Payment #'.$payment->id }}
                                </div>
                                <div class="mt-1 text-sm text-slate-400">
                                    {{ $payment->payable?->student?->full_name ?: 'Linked student' }}
                                </div>
                            </div>
                            <div class="text-sm text-slate-300">
                                <div>{{ strtoupper($payment->status) }} - {{ $money($payment->amount) }}</div>
                                <div class="mt-1 text-slate-400">
                                    {{ $payment->receipt?->receipt_number ?: 'Receipt pending' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-sm text-slate-400">
                        No payment history is available for the linked invoices yet.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-guardian-layout>
