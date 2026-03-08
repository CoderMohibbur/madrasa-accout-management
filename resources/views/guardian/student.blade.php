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
        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xs uppercase tracking-[0.2em] text-emerald-300">Linked Student</div>
                    <h2 class="mt-2 text-2xl font-semibold text-white">{{ $student->full_name ?: 'Student #'.$student->id }}</h2>
                </div>

                <span class="rounded-full border border-white/10 px-3 py-1 text-xs font-medium text-slate-300">
                    {{ $student->pivot->relationship_label ?: 'Guardian link' }}
                </span>
            </div>

            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-4">
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Roll</dt>
                    <dd class="mt-2 text-lg font-semibold text-white">{{ $student->roll ?: '-' }}</dd>
                </div>
                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-4">
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Academic Year</dt>
                    <dd class="mt-2 text-lg font-semibold text-white">{{ $yearLabel }}</dd>
                </div>
                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-4">
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Class / Section</dt>
                    <dd class="mt-2 text-lg font-semibold text-white">{{ $classLabel }} / {{ $sectionLabel }}</dd>
                </div>
                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-4">
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Fees Type</dt>
                    <dd class="mt-2 text-lg font-semibold text-white">{{ $feesTypeLabel }}</dd>
                </div>
                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-4">
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Father Name</dt>
                    <dd class="mt-2 text-lg font-semibold text-white">{{ $student->father_name ?: '-' }}</dd>
                </div>
                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-4">
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Mobile</dt>
                    <dd class="mt-2 text-lg font-semibold text-white">{{ $student->mobile ?: '-' }}</dd>
                </div>
                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-4 sm:col-span-2">
                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Address</dt>
                    <dd class="mt-2 text-base leading-6 text-slate-300">{{ $student->address ?: 'No address has been recorded.' }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-[2rem] border border-white/10 bg-slate-900/70">
            <div class="flex items-center justify-between border-b border-white/10 px-6 py-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">Invoices</h2>
                    <p class="mt-1 text-sm text-slate-400">Only invoices visible through the guardian-safe ownership policy are listed.</p>
                </div>
            </div>

            <div class="divide-y divide-white/10">
                @forelse ($invoices as $invoice)
                    <a href="{{ route('guardian.invoices.show', $invoice) }}" class="block px-6 py-5 transition hover:bg-white/5">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <div class="font-semibold text-white">{{ $invoice->invoice_number }}</div>
                                <div class="mt-1 text-sm text-slate-400">
                                    Due {{ $invoice->due_at?->format('Y-m-d') ?: 'Not scheduled' }} - {{ strtoupper($invoice->status) }}
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
                        No linked invoices are available for this student yet.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <section class="mt-6 rounded-[2rem] border border-white/10 bg-slate-900/70">
        <div class="flex items-center justify-between border-b border-white/10 px-6 py-5">
            <div>
                <h2 class="text-lg font-semibold text-white">Recent Payment History</h2>
                <p class="mt-1 text-sm text-slate-400">Read-only payment and receipt visibility for this linked student.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10 text-sm">
                <thead class="bg-white/5 text-left text-xs uppercase tracking-[0.2em] text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Invoice</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Receipt</th>
                        <th class="px-6 py-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($payments as $payment)
                        <tr class="text-slate-300">
                            <td class="px-6 py-4">{{ $payment->payable?->invoice_number ?: 'Payment #'.$payment->id }}</td>
                            <td class="px-6 py-4">{{ strtoupper($payment->status) }}</td>
                            <td class="px-6 py-4">{{ $money($payment->amount) }}</td>
                            <td class="px-6 py-4">{{ $payment->receipt?->receipt_number ?: 'Receipt pending' }}</td>
                            <td class="px-6 py-4">
                                {{ optional($payment->paid_at ?? $payment->initiated_at ?? $payment->created_at)->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-400">
                                No payment activity has been recorded for this student's linked invoices yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-guardian-layout>
