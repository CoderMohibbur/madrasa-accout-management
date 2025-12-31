<form method="POST" action="{{ route('fees.bulk_store') }}" class="space-y-3">
    @csrf

    <input type="hidden" name="type_key" value="student_fee">
    <input type="hidden" name="_tc_fee_mode" value="bulk">

    @php
        $isBulkOld = old('_tc_fee_mode') === 'bulk';
        $oldYear    = $isBulkOld ? old('academic_year_id') : null;
        $oldMonth   = $isBulkOld ? old('months_id') : null;
        $oldClass   = $isBulkOld ? old('class_id') : null;
        $oldSection = $isBulkOld ? old('section_id') : null;
        $oldAccount = $isBulkOld ? old('account_id') : null;
    @endphp

    <div class="grid grid-cols-2 gap-2">
        <div>
            <label class="text-xs text-slate-500">Academic Year</label>
            <select id="tc_academic_year_id" name="academic_year_id" class="w-full rounded border-slate-200">
                <option value="">Select year</option>
                @foreach (($years ?? []) as $year)
                    @php $label = $year->year ?? $year->name ?? $year->title ?? ('Year #'.$year->id); @endphp
                    <option value="{{ $year->id }}" @selected((string)$oldYear === (string)$year->id)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-xs text-slate-500">Month</label>
            <select id="tc_months_id" name="months_id" class="w-full rounded border-slate-200">
                <option value="">Select month</option>
                @foreach (($months ?? []) as $month)
                    <option value="{{ $month->id }}" @selected((string)$oldMonth === (string)$month->id)>
                        {{ $month->name ?? $month->month ?? ('Month #'.$month->id) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-xs text-slate-500">Class</label>
            <select id="tc_class_id" name="class_id" class="w-full rounded border-slate-200">
                <option value="">Select class</option>
                @foreach (($classes ?? []) as $class)
                    <option value="{{ $class->id }}" @selected((string)$oldClass === (string)$class->id)>
                        {{ $class->name ?? $class->class_name ?? ('Class #'.$class->id) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-xs text-slate-500">Section</label>
            <select id="tc_section_id" name="section_id" class="w-full rounded border-slate-200">
                <option value="">Select section</option>
                @foreach (($sections ?? []) as $section)
                    <option value="{{ $section->id }}" @selected((string)$oldSection === (string)$section->id)>
                        {{ $section->name ?? $section->section_name ?? ('Section #'.$section->id) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-span-2">
            <label class="text-xs text-slate-500">Account</label>
            <div class="flex gap-2">
                <select id="tc_bulk_account" data-entity-select="accounts" name="account_id" class="w-full rounded border-slate-200">
                    <option value="">Select account</option>
                    @foreach (($accounts ?? []) as $acc)
                        <option value="{{ $acc->id }}" @selected((string)$oldAccount === (string)$acc->id)>
                            {{ $acc->name ?? 'Account #'.$acc->id }}
                        </option>
                    @endforeach
                </select>

                <button type="button" @click="openModal('accounts', 'tc_bulk_account')" class="shrink-0 rounded border px-2 text-sm">
                    + Add
                </button>
            </div>
        </div>

        <div class="col-span-2">
            <label class="text-xs text-slate-500">Transaction Date (optional)</label>
            <input type="date" name="transactions_date"
                value="{{ old('transactions_date', now()->toDateString()) }}"
                class="w-full rounded border-slate-200" />
        </div>
    </div>

    <div class="flex items-center gap-2">
        <button type="button" onclick="tcLoadStudents()"
            class="rounded bg-slate-900 px-3 py-2 text-white text-sm">
            Load Students
        </button>

        {{-- ✅ Save Fees: students load না হলে disable থাকবে --}}
        <button id="tc_save_btn" type="submit" disabled
            class="rounded bg-emerald-600 px-3 py-2 text-white text-sm disabled:opacity-50 disabled:cursor-not-allowed">
            Save Fees
        </button>
    </div>

    <div id="tc_students_status"
        class="hidden rounded border border-rose-200 bg-rose-50 p-2 text-xs text-rose-700"></div>

    <div class="rounded border border-slate-200 p-2">
        <div class="text-xs text-slate-500 mb-2">
            Students list (auto fill if already saved for this month)
        </div>

        <div id="tc_students_list" class="space-y-2 max-h-[55vh] overflow-auto"></div>
    </div>
</form>

<script>
function tcSetSaveEnabled(enabled){
    const btn = document.getElementById('tc_save_btn');
    if(btn) btn.disabled = !enabled;
}

document.addEventListener('DOMContentLoaded', () => {
    tcSetSaveEnabled(false);
});

function tcShowStatus(html){
    const box = document.getElementById('tc_students_status');
    if(!box) return;
    box.classList.remove('hidden');
    box.innerHTML = html;
}
function tcHideStatus(){
    const box = document.getElementById('tc_students_status');
    if(!box) return;
    box.classList.add('hidden');
    box.innerHTML = '';
}

async function tcLoadStudents(){
    tcHideStatus();
    tcSetSaveEnabled(false);

    const academicYearId = document.getElementById('tc_academic_year_id')?.value || '';
    const monthId        = document.getElementById('tc_months_id')?.value || '';
    const classId        = document.getElementById('tc_class_id')?.value || '';
    const sectionId      = document.getElementById('tc_section_id')?.value || '';

    if(!academicYearId || !monthId || !classId || !sectionId){
        tcShowStatus('Please select <b>Academic Year, Month, Class, Section</b> then click <b>Load Students</b>.');
        return;
    }

    const url = new URL(@json(route('get.students')));
    url.searchParams.set('academic_year_id', academicYearId);
    url.searchParams.set('months_id', monthId);
    url.searchParams.set('class_id', classId);
    url.searchParams.set('section_id', sectionId);

    const list = document.getElementById('tc_students_list');
    if(list) list.innerHTML = '<div class="text-xs text-slate-500 p-2">Loading...</div>';

    try{
        const res = await fetch(url.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });

        const ct = (res.headers.get('content-type') || '').toLowerCase();

        if(!res.ok){
            if(ct.includes('application/json')){
                const data = await res.json();
                const errs = data.errors || {};
                const items = Object.values(errs).flat().map(e => `<li>${e}</li>`).join('');
                tcShowStatus(`<b>Could not load students</b><ul class="list-disc ml-5 mt-1">${items || data.message || 'Unknown error'}</ul>`);
            }else{
                const txt = await res.text();
                tcShowStatus(`<b>Could not load students</b><div class="mt-1">${txt}</div>`);
            }
            if(list) list.innerHTML = '';
            return;
        }

        const html = await res.text();
        if(list) list.innerHTML = html;

        // ✅ student_ids[] আছে কিনা চেক
        const hasStudents = !!list?.querySelector('input[name="student_ids[]"]');
        if(!hasStudents){
            tcShowStatus('No students found for this Academic Year / Class / Section. (Students table এ academic_year_id, class_id, section_id সেট আছে কিনা চেক করুন)');
            tcSetSaveEnabled(false);
            return;
        }

        tcSetSaveEnabled(true);
        tcWireFeeAutoCalc();

    }catch(e){
        tcShowStatus('Network error while loading students.');
        if(list) list.innerHTML = '';
    }
}

function tcWireFeeAutoCalc(){
    const wrap = document.getElementById('tc_students_list');
    if(!wrap) return;

    wrap.querySelectorAll('.student-fee').forEach((row) => {
        const inputs = row.querySelectorAll(
          'input[name="monthly_fees[]"], input[name="boarding_fees[]"], input[name="management_fees[]"], input[name="exam_fees[]"], input[name="others_fees[]"]'
        );
        const total  = row.querySelector('input[name="total_fees[]"]');
        const debit  = row.querySelector('input[name="debit[]"]');

        const calc = () => {
            let sum = 0;
            inputs.forEach(i => sum += (parseFloat(i.value || 0) || 0));
            if(total) total.value = sum.toFixed(2);
            if(debit) debit.value = sum.toFixed(2);
        };

        inputs.forEach(i => i.addEventListener('input', calc));
    });
}
</script>
