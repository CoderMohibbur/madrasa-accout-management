
@foreach($students as $student)

    <div class="student-fee flex">
        <input type="text" value="{{ $student->full_name }}" disabled/>
        <input type="hidden" name="student_ids[]" value="{{ $student->id }}" />
        <input type="number" name="monthly_fees[]" placeholder="Monthly Fees" />
        <input type="number" name="boarding_fees[]" placeholder="Boarding Fees" />
        <input type="number" name="management_fees[]" placeholder="Management Fees" />
        <input type="number" name="exam_fees[]" placeholder=" Exam Fees" />
        <input type="number" name="others_fees[]" placeholder="Others Fees" />
        <input type="number" name="total_fees[]" placeholder="Total Fees" />
        <input type="text" name="note[]" placeholder="note" />
    </div>
@endforeach
