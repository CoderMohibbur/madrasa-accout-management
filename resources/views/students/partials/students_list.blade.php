@php
    $students = $students ?? collect();
    $transactions = $transactions ?? collect(); // keyed by student_id
@endphp

@if($students->isEmpty())
    <div class="rounded-lg border border-amber-200 bg-amber-50 p-2 text-xs text-amber-800">
        No students found for selected Academic Year / Class / Section.
    </div>
@else
    @foreach($students as $s)
        @php
            $tx = $transactions[$s->id] ?? null;
            $studentName = $s->name ?? ($s->student_name ?? ($s->full_name ?? ('Student #'.$s->id)));
        @endphp

        <div class="student-fee rounded-xl border border-slate-200 bg-white p-2 space-y-2">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <div class="text-sm font-semibold text-slate-800">{{ $studentName }}</div>
                    <div class="text-[11px] text-slate-500">ID: {{ $s->id }}</div>
                </div>
                <div class="text-[11px] text-slate-500">
                    Prev: {{ number_format((float)($tx->total_fees ?? 0), 2) }}
                </div>
            </div>

            {{-- ✅ important --}}
            <input type="hidden" name="student_ids[]" value="{{ $s->id }}">

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Monthly</label>
                    <input type="number" step="0.01" name="monthly_fees[]"
                        value="{{ $tx->monthly_fees ?? 0 }}"
                        class="w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Boarding</label>
                    <input type="number" step="0.01" name="boarding_fees[]"
                        value="{{ $tx->boarding_fees ?? 0 }}"
                        class="w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Management</label>
                    <input type="number" step="0.01" name="management_fees[]"
                        value="{{ $tx->management_fees ?? 0 }}"
                        class="w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Exam</label>
                    <input type="number" step="0.01" name="exam_fees[]"
                        value="{{ $tx->exam_fees ?? 0 }}"
                        class="w-full rounded-lg border-slate-200 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Others</label>
                    <input type="number" step="0.01" name="others_fees[]"
                        value="{{ $tx->others_fees ?? 0 }}"
                        class="w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Receipt No</label>
                    <input type="text" name="recipt_no[]"
                        value="{{ $tx->recipt_no ?? '' }}"
                        class="w-full rounded-lg border-slate-200 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Total</label>
                    <input type="number" step="0.01" name="total_fees[]"
                        value="{{ $tx->total_fees ?? 0 }}"
                        class="w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Debit</label>
                    <input type="number" step="0.01" name="debit[]"
                        value="{{ $tx->debit ?? 0 }}"
                        class="w-full rounded-lg border-slate-200 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-[11px] text-slate-500 mb-1">Note</label>
                <input type="text" name="note[]"
                    value="{{ $tx->note ?? '' }}"
                    class="w-full rounded-lg border-slate-200 text-sm">
            </div>
        </div>
    @endforeach
@endif
