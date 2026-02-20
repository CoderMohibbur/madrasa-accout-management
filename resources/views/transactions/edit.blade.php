{{-- resources/views/transactions/edit.blade.php --}}
@php
    $typeKey  = data_get($transaction, 'type.key');
    $typeName = data_get($transaction, 'type.name') ?? '-';

    // ✅ fallback (legacy type row without key) — same logic as controller
    if (!$typeKey) {
        $name = strtolower((string) $typeName);
        if (str_contains($name, 'student') && str_contains($name, 'fee')) $typeKey = 'student_fee';
        elseif (str_contains($name, 'don')) $typeKey = 'donation';
        elseif (str_contains($name, 'repay')) $typeKey = 'loan_repayment';
        elseif (str_contains($name, 'loan')) $typeKey = 'loan_taken';
        elseif (str_contains($name, 'expense')) $typeKey = 'expense';
        elseif (str_contains($name, 'income')) $typeKey = 'income';
    }

    // Amount = debit or credit whichever is positive
    $isDebit = ((float) ($transaction->debit ?? 0)) > 0;
    $amount  = $isDebit ? (float) ($transaction->debit ?? 0) : (float) ($transaction->credit ?? 0);

    // expense/income/loan/donation title stored at c_s_1
    $title = $transaction->c_s_1 ?? null;

    // admission fee stored at c_d_1
    $admission = (float) ($transaction->c_d_1 ?? 0);

    $inputClass  = 'w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500';
    $selectClass = $inputClass;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">Edit Transaction</h2>
                <p class="text-xs text-slate-500 mt-1">
                    #{{ $transaction->id }} — {{ $typeName }}
                    @if ($typeKey)
                        <span class="ml-2 inline-flex items-center rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-[11px] text-slate-600">
                            key: {{ $typeKey }}
                        </span>
                    @endif
                </p>
            </div>

            <a href="{{ url('/transaction-center') }}"
               class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm hover:bg-slate-50">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800 text-sm">
                    <div class="font-semibold mb-1">Fix these:</div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('transactions.update', $transaction->id) }}"
                  class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 space-y-5">
                @csrf
                @method('PUT')

                {{-- Top fields --}}
                <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-12 sm:col-span-6">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Account</label>
                        <select name="account_id" class="{{ $selectClass }}">
                            @foreach ($accounts ?? [] as $acc)
                                <option value="{{ $acc->id }}"
                                    @selected((string) $acc->id === (string) old('account_id', $transaction->account_id))>
                                    {{ $acc->name ?? 'Account #' . $acc->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Date</label>
                        <input type="date" name="transactions_date"
                               value="{{ old('transactions_date', $transaction->transactions_date) }}"
                               class="{{ $inputClass }}">
                    </div>

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Receipt No</label>
                        <input type="text" name="recipt_no"
                               value="{{ old('recipt_no', $transaction->recipt_no) }}"
                               class="{{ $inputClass }}">
                    </div>
                </div>

                {{-- Type-wise --}}
                @if ($typeKey === 'student_fee')
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/30 p-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold text-slate-800">Student Fee Details</div>
                                <div class="text-xs text-slate-500">Update student + month + fees breakdown</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-3">
                            <div class="col-span-12 sm:col-span-6">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Student</label>
                                <select name="student_id" class="{{ $selectClass }}">
                                    <option value="">Select student</option>
                                    @foreach ($students ?? [] as $s)
                                        @php
                                            $label = $s->full_name ?? ($s->name ?? 'Student #' . $s->id);
                                            $label = $s->roll ? $label . ' (Roll: ' . $s->roll . ')' : $label;
                                        @endphp
                                        <option value="{{ $s->id }}"
                                            @selected((string) $s->id === (string) old('student_id', $transaction->student_id))>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-12 sm:col-span-6">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Month</label>
                                <select name="months_id" class="{{ $selectClass }}">
                                    <option value="">Select month</option>
                                    @foreach ($months ?? [] as $m)
                                        <option value="{{ $m->id }}"
                                            @selected((string) $m->id === (string) old('months_id', $transaction->months_id))>
                                            {{ $m->name ?? 'Month #' . $m->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Meta (optional but editable) --}}
                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Academic Year</label>
                                <select name="academic_year_id" class="{{ $selectClass }}">
                                    <option value="">Select year</option>
                                    @foreach ($years ?? [] as $y)
                                        <option value="{{ $y->id }}"
                                            @selected((string) $y->id === (string) old('academic_year_id', $transaction->academic_year_id))>
                                            {{ $y->name ?? 'Year #' . $y->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Class</label>
                                <select name="class_id" class="{{ $selectClass }}">
                                    <option value="">Select class</option>
                                    @foreach ($classes ?? [] as $c)
                                        <option value="{{ $c->id }}"
                                            @selected((string) $c->id === (string) old('class_id', $transaction->class_id))>
                                            {{ $c->name ?? 'Class #' . $c->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Section</label>
                                <select name="section_id" class="{{ $selectClass }}">
                                    <option value="">Select section</option>
                                    @foreach ($sections ?? [] as $sec)
                                        <option value="{{ $sec->id }}"
                                            @selected((string) $sec->id === (string) old('section_id', $transaction->section_id))>
                                            {{ $sec->name ?? 'Section #' . $sec->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Fees Type</label>
                                <select name="fess_type_id" class="{{ $selectClass }}">
                                    <option value="">Select type</option>
                                    @foreach ($feesTypes ?? [] as $ft)
                                        <option value="{{ $ft->id }}"
                                            @selected((string) $ft->id === (string) old('fess_type_id', $transaction->fess_type_id))>
                                            {{ $ft->name ?? 'Type #' . $ft->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-12 sm:col-span-6">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Student Book Number</label>
                                <input type="text" name="student_book_number"
                                       value="{{ old('student_book_number', $transaction->student_book_number) }}"
                                       class="{{ $inputClass }}">
                            </div>

                            {{-- Fees --}}
                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Admission Fee</label>
                                <input type="number" step="0.01" min="0" name="admission_fee"
                                       value="{{ old('admission_fee', $admission) }}"
                                       class="tc-fee-input {{ $inputClass }}">
                            </div>

                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Monthly Fee</label>
                                <input type="number" step="0.01" min="0" name="monthly_fees"
                                       value="{{ old('monthly_fees', $transaction->monthly_fees) }}"
                                       class="tc-fee-input {{ $inputClass }}">
                            </div>

                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Boarding Fee</label>
                                <input type="number" step="0.01" min="0" name="boarding_fees"
                                       value="{{ old('boarding_fees', $transaction->boarding_fees) }}"
                                       class="tc-fee-input {{ $inputClass }}">
                            </div>

                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Management Fee</label>
                                <input type="number" step="0.01" min="0" name="management_fees"
                                       value="{{ old('management_fees', $transaction->management_fees) }}"
                                       class="tc-fee-input {{ $inputClass }}">
                            </div>

                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Exam Fee</label>
                                <input type="number" step="0.01" min="0" name="exam_fees"
                                       value="{{ old('exam_fees', $transaction->exam_fees) }}"
                                       class="tc-fee-input {{ $inputClass }}">
                            </div>

                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Others</label>
                                <input type="number" step="0.01" min="0" name="others_fees"
                                       value="{{ old('others_fees', $transaction->others_fees) }}"
                                       class="tc-fee-input {{ $inputClass }}">
                            </div>

                            <div class="col-span-12 sm:col-span-6">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Total Fee (Auto)</label>
                                <input id="tc_edit_total" type="number" step="0.01" min="0" name="total_fees"
                                       value="{{ old('total_fees', $transaction->total_fees) }}"
                                       class="w-full rounded-xl border-slate-200 text-sm bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                       readonly>
                                <p class="text-[11px] text-slate-500 mt-1">Auto calculated from fee fields.</p>
                            </div>
                        </div>
                    </div>

                @elseif ($typeKey === 'donation')
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/30 p-4 space-y-4">
                        <div>
                            <div class="text-sm font-semibold text-slate-800">Donation Details</div>
                            <div class="text-xs text-slate-500">Update donor + amount</div>
                        </div>

                        <div class="grid grid-cols-12 gap-3">
                            <div class="col-span-12 sm:col-span-6">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Donor</label>
                                <select name="doner_id" class="{{ $selectClass }}">
                                    <option value="">Select donor</option>
                                    @foreach ($donors ?? [] as $d)
                                        @php
                                            $label = $d->name ?? ($d->donor_name ?? ($d->doner_name ?? 'Donor #' . $d->id));
                                        @endphp
                                        <option value="{{ $d->id }}"
                                            @selected((string) $d->id === (string) old('doner_id', $transaction->doner_id))>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-12 sm:col-span-6">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Amount</label>
                                <input type="number" step="0.01" min="0" name="amount"
                                       value="{{ old('amount', $amount) }}"
                                       class="{{ $inputClass }}">
                            </div>

                            <div class="col-span-12">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Title (optional)</label>
                                <input type="text" name="title"
                                       value="{{ old('title', $title) }}"
                                       placeholder="Donation"
                                       class="{{ $inputClass }}">
                            </div>
                        </div>
                    </div>

                @elseif ($typeKey === 'loan_taken' || $typeKey === 'loan_repayment')
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/30 p-4 space-y-4">
                        <div>
                            <div class="text-sm font-semibold text-slate-800">
                                {{ $typeKey === 'loan_taken' ? 'Loan Taken Details' : 'Loan Repayment Details' }}
                            </div>
                            <div class="text-xs text-slate-500">Update lender + amount</div>
                        </div>

                        <div class="grid grid-cols-12 gap-3">
                            <div class="col-span-12 sm:col-span-6">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Lender</label>
                                <select name="lender_id" class="{{ $selectClass }}">
                                    <option value="">Select lender</option>
                                    @foreach ($lenders ?? [] as $l)
                                        @php $label = $l->name ?? ($l->lender_name ?? 'Lender #' . $l->id); @endphp
                                        <option value="{{ $l->id }}"
                                            @selected((string) $l->id === (string) old('lender_id', $transaction->lender_id))>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-12 sm:col-span-6">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Amount</label>
                                <input type="number" step="0.01" min="0" name="amount"
                                       value="{{ old('amount', $amount) }}"
                                       class="{{ $inputClass }}">
                            </div>

                            <div class="col-span-12">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Title (optional)</label>
                                <input type="text" name="title"
                                       value="{{ old('title', $title) }}"
                                       placeholder="{{ $typeKey === 'loan_taken' ? 'Loan Taken' : 'Loan Repayment' }}"
                                       class="{{ $inputClass }}">
                            </div>
                        </div>
                    </div>

                @elseif ($typeKey === 'expense' || $typeKey === 'income')
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/30 p-4 space-y-4">
                        <div>
                            <div class="text-sm font-semibold text-slate-800">
                                {{ $typeKey === 'expense' ? 'Expense Details' : 'Income Details' }}
                            </div>
                            <div class="text-xs text-slate-500">Update title + amount</div>
                        </div>

                        <div class="grid grid-cols-12 gap-3">
                            <div class="col-span-12 sm:col-span-6">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Title</label>
                                <input type="text" name="title"
                                       value="{{ old('title', $title) }}"
                                       class="{{ $inputClass }}">
                            </div>

                            <div class="col-span-12 sm:col-span-6">
                                <label class="block text-xs font-medium text-slate-600 mb-1">Amount</label>
                                <input type="number" step="0.01" min="0" name="amount"
                                       value="{{ old('amount', $amount) }}"
                                       class="{{ $inputClass }}">
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Note --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Note</label>
                    <input type="text" name="note"
                           value="{{ old('note', $transaction->note) }}"
                           class="{{ $inputClass }}">
                </div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <a href="{{ url('/transaction-center') }}"
                       class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm hover:bg-slate-800">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ✅ Auto total for student_fee (includes admission_fee too)
        document.addEventListener('DOMContentLoaded', () => {
            const totalEl = document.getElementById('tc_edit_total');
            const inputs = document.querySelectorAll('.tc-fee-input');
            if (!totalEl || !inputs.length) return;

            const calc = () => {
                let sum = 0;
                inputs.forEach(i => sum += (parseFloat(i.value || 0) || 0));
                totalEl.value = sum > 0 ? sum.toFixed(2) : '';
            };

            inputs.forEach(i => i.addEventListener('input', calc));
            calc();
        });
    </script>
</x-app-layout>
