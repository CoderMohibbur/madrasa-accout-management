@php
    $students = $students ?? collect();
    $transactions = $transactions ?? collect(); // keyed by student_id
    $df = $defaultFee ?? null;

    // default fees (match your AddRegistrationFess columns)
    $dfMonthly = (float) ($df->monthly_fee ?? 0);
    $dfBoarding = (float) ($df->boarding_fee ?? 0);
    $dfManagement = (float) ($df->management_fee ?? 0);
    $dfExam = (float) ($df->examination_fee ?? 0);
    $dfOthers = (float) ($df->other ?? 0);
@endphp

@if ($students->isEmpty())
    <div class="text-xs text-slate-500 p-2">No students found.</div>
@else
    @foreach ($students as $s)
        @php
            $tx = $transactions[$s->id] ?? null;
            $isPaid = (bool) $tx;

            $admission = (float) ($tx->c_d_1 ?? 0);
            $monthly = (float) ($tx->monthly_fees ?? $dfMonthly);
            $boarding = (float) ($tx->boarding_fees ?? $dfBoarding);
            $management = (float) ($tx->management_fees ?? $dfManagement);
            $exam = (float) ($tx->exam_fees ?? $dfExam);
            $others = (float) ($tx->others_fees ?? $dfOthers);
            $total = (float) ($tx->total_fees ?? $admission + $monthly + $boarding + $management + $exam + $others);
        @endphp

        <div class="student-fee rounded-xl border border-slate-200 p-3 bg-white space-y-2">
            {{-- needed for JS hasStudents check --}}
            <input type="hidden" name="student_ids[]" value="{{ $s->id }}">

            <div class="flex items-start justify-between gap-2">
                <div>
                    <div class="text-sm font-semibold text-slate-800">{{ $s->full_name ?? 'Student' }}</div>
                    <div class="text-[11px] text-slate-500">Roll: {{ $s->roll ?? '-' }} • ID: {{ $s->id }}</div>
                </div>

                <div class="flex items-center gap-2">
                    @if ($isPaid)
                        <span
                            class="text-[11px] px-2 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700">
                            Paid (already)
                        </span>
                    @endif

                    <label class="inline-flex items-center gap-2 text-xs">
                        <input type="checkbox" name="paid_ids[]" value="{{ $s->id }}"
                            class="rounded border-slate-200" @disabled($isPaid)>
                        Paid
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                <div>
                    <label class="text-[11px] text-slate-500">Admission</label>
                    <input data-fee type="number" step="0.01" name="rows[{{ $s->id }}][admission_fee]"
                        value="{{ number_format($admission, 2, '.', '') }}" class="w-full rounded border-slate-200"
                        @disabled($isPaid)>
                </div>

                <div>
                    <label class="text-[11px] text-slate-500">Monthly</label>
                    <input data-fee type="number" step="0.01" name="rows[{{ $s->id }}][monthly_fees]"
                        value="{{ number_format($monthly, 2, '.', '') }}" class="w-full rounded border-slate-200"
                        @disabled($isPaid)>
                </div>

                <div>
                    <label class="text-[11px] text-slate-500">Boarding</label>
                    <input data-fee type="number" step="0.01" name="rows[{{ $s->id }}][boarding_fees]"
                        value="{{ number_format($boarding, 2, '.', '') }}" class="w-full rounded border-slate-200"
                        @disabled($isPaid)>
                </div>

                <div>
                    <label class="text-[11px] text-slate-500">Management</label>
                    <input data-fee type="number" step="0.01" name="rows[{{ $s->id }}][management_fees]"
                        value="{{ number_format($management, 2, '.', '') }}" class="w-full rounded border-slate-200"
                        @disabled($isPaid)>
                </div>

                <div>
                    <label class="text-[11px] text-slate-500">Exam</label>
                    <input data-fee type="number" step="0.01" name="rows[{{ $s->id }}][exam_fees]"
                        value="{{ number_format($exam, 2, '.', '') }}" class="w-full rounded border-slate-200"
                        @disabled($isPaid)>
                </div>

                <div>
                    <label class="text-[11px] text-slate-500">Others</label>
                    <input data-fee type="number" step="0.01" name="rows[{{ $s->id }}][others_fees]"
                        value="{{ number_format($others, 2, '.', '') }}" class="w-full rounded border-slate-200"
                        @disabled($isPaid)>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-[11px] text-slate-500">Total</label>
                    <input data-total type="number" step="0.01" name="rows[{{ $s->id }}][total_fees]"
                        value="{{ number_format($total, 2, '.', '') }}" class="w-full rounded border-slate-200"
                        @disabled($isPaid)>
                </div>
                <div>
                    <label class="text-[11px] text-slate-500">Note</label>
                    <input type="text" name="rows[{{ $s->id }}][note]" value="{{ $tx->note ?? '' }}"
                        class="w-full rounded border-slate-200" @disabled($isPaid)>
                </div>
            </div>
        </div>
    @endforeach
@endif
