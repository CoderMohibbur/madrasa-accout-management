@foreach($students as $student)
    @php
        // Get the transaction for this student if it exists
        $transaction = $transactions->get($student->id);
    @endphp

    <div class="student-fee flex">
        <input type="text" value="{{ $student->full_name }}" disabled/>
        <input type="hidden" name="student_ids[]" value="{{ $student->id }}" />
        <input type="text" name="student_book_number[]" placeholder="Student Book Number" value="{{ $transaction->student_book_number ?? '' }}" />
        <input type="text" name="recipt_no[]" placeholder="Receipt No" value="{{ $transaction->recipt_no ?? '' }}" />
        <input type="number" name="monthly_fees[]" placeholder="Monthly Fees" value="{{ $transaction->monthly_fees ?? '' }}" />
        <input type="number" name="boarding_fees[]" placeholder="Boarding Fees" value="{{ $transaction->boarding_fees ?? '' }}" />
        <input type="number" name="management_fees[]" placeholder="Management Fees" value="{{ $transaction->management_fees ?? '' }}" />
        <input type="number" name="exam_fees[]" placeholder="Exam Fees" value="{{ $transaction->exam_fees ?? '' }}" />
        <input type="number" name="others_fees[]" placeholder="Others Fees" value="{{ $transaction->others_fees ?? '' }}" />
        <input type="number" name="total_fees[]" placeholder="Total Fees" value="{{ $transaction->total_fees ?? '' }}" />
        <input type="number" name="debit[]" placeholder="Debit" value="{{ $transaction->debit ?? '' }}" />
        <input type="number" name="credit[]" placeholder="Credit" value="{{ $transaction->credit ?? '' }}" />
        <input type="text" name="note[]" placeholder="Note" value="{{ $transaction->note ?? '' }}" />
    </div>
@endforeach
