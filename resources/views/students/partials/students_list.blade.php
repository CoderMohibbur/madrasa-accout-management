
@foreach($students as $student)

    <div class="student-fee">
        <input type="text" value="{{ $student->full_name }}" />
        <input type="hidden" name="student_ids[]" value="{{ $student->id }}" />
        <input type="number" name="monthly_fees[]" placeholder="Monthly Fees" />
        <input type="number" name="boarding_fees[]" placeholder="Boarding Fees" />
        <!-- আরও ফিস ফিল্ড -->
    </div>
@endforeach