<?php

namespace App\Http\Controllers;

use id;
use App\Models\User;
use App\Models\Account;
use App\Models\Student;
use App\Models\AddClass;
use App\Models\AddMonth;
use App\Models\AddAcademy;
use App\Models\AddSection;
use App\Models\AddFessType;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\TransactionsType;

class TransactionsController extends Controller
{
    //
    public function index()
    {
        // Fetch all classes
        $transactionss = Transactions::all();
        $students = Student::all();
        $accounts = Account::all();
        $classes = AddClass::all();
        $sections = AddSection::all();
        $months = AddMonth::all();
        $years = AddAcademy::all();
        $feestypes = AddFessType::all();
        $transactionss = TransactionsType::all();
        $users = User::all();

        // Return view with the list of classes
        return view('students.add-fees', compact('transactionss', 'students', 'accounts', 'classes', 'sections', 'years', 'months', 'transactionss', 'users'));

    }

    public function list()
    {
        // Fetch all classes
        $transactionss = Transactions::all();
        $students = Student::all();
        $accounts = Account::all();
        $classes = AddClass::all();
        $sections = AddSection::all();
        $months = AddMonth::all();
        $years = AddAcademy::all();
        $feestypes = AddFessType::all();
        $transactionss = TransactionsType::all();
        $users = User::all();

        // Return view with the list of classes
        return view('students.list-fees', compact('transactionss', 'students', 'accounts', 'classes', 'sections', 'years', 'months', 'transactionss', 'users'));

    }

    public function fetchStudents(Request $request)
    {
        $academicYearId = $request->input('academic_year_id');
        $monthId = $request->input('months_id');
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');

        // Fetch students based on the filters
        $students = Student::where('academic_year_id', $academicYearId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->get();

        // Return the student data as JSON
        return response()->json($students);
    }

    public function getStudents(Request $request)
    {
        $academicYearId = $request->academic_year_id;
        $classId = $request->class_id;
        $sectionId = $request->section_id;
        $monthId = $request->months_id;

        // Fetch students based on the selected filters
        $students = Student::where('academic_year_id', $academicYearId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->get();

        // Fetch transactions for the selected students and filters
        $transactions = Transactions::whereIn('student_id', $students->pluck('id'))
            ->where('academic_year_id', $academicYearId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->where('months_id', $monthId)
            ->get()
            ->keyBy('student_id'); // Group by student_id for easy access

        // Pass students and transactions data to the view
        return view('students.partials.students_list', compact('students', 'transactions'));
    }

    public function store(Request $request)
    {
        // Validate the form inputs
        $validatedData = $request->validate([
            'student_id' => 'exists:students,id',
            'fess_type_id' => 'exists:add_fess_types,id',
            'transactions_type_id' => 'exists:transactions_types,id',
            'student_book_number' => 'string|max:255',
            'recipt_no' => 'integer',
            'monthly_fees' => 'numeric',
            'boarding_fees' => 'numeric',
            'management_fees' => 'numeric',
            'exam_fees' => 'numeric',
            'others_fees' => 'numeric',
            'total_fees' => 'numeric',
            'debit' => 'numeric',
            'credit' => 'numeric',
            'transactions_date' => 'date',
            'account_id' => 'exists:accounts,id',
            'class_id' => 'exists:add_classes,id',
            'section_id' => 'exists:add_sections,id',
            'months_id' => 'exists:add_months,id',
            'academic_year_id' => 'exists:add_academies,id',
            'created_by_id' => 'exists:users,id',
            'note' => 'string|max:500',
            'isActived' => 'required|boolean', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database

        Transactions::create([
            'student_id' => $validatedData['student_id'],
            'fess_type_id' => $validatedData['fess_type_id'],
            'transactions_type_id' => $validatedData['transactions_type_id'],
            'student_book_number' => $validatedData['student_book_number'],
            'recipt_no' => $validatedData['recipt_no'],
            'monthly_fees' => $validatedData['monthly_fees'],
            'boarding_fees' => $validatedData['boarding_fees'],
            'management_fees' => $validatedData['management_fees'],
            'exam_fees' => $validatedData['exam_fees'],
            'others_fees' => $validatedData['others_fees'],
            'total_fees' => $validatedData['total_fees'],
            'debit' => $validatedData['debit'],
            'credit' => $validatedData['credit'],
            'transactions_date' => $validatedData['transactions_date'],
            'account_id' => $validatedData['account_id'],
            'class_id' => $validatedData['class_id'],
            'section_id' => $validatedData['section_id'],
            'months_id' => $validatedData['months_id'],
            'academic_year_id' => $validatedData['academic_year_id'],
            'created_by_id' => $validatedData['created_by_id'],
            'note' => $validatedData['note'],
            'isActived' => $validatedData['isActived'] ?? 1, // Save as boolean: true for 'activate', false for 'deactivate'
        ]);

        // Redirect back or to a success page
        return redirect()->route('add_fees_type.index')->with('success', 'Class added successfully!');
    }

    public function bulkStore(Request $request)
    {
        // Validate the form inputs
        $validatedData = $request->validate([
            'student_ids.*' => 'exists:students,id',
            // 'fess_type_id' => 'exists:add_fess_types,id',
            // 'transactions_type_id' => 'exists:transactions_types,id',
            'student_book_number.*' => 'nullable|string|max:255',
            'recipt_no.*' => 'nullable|integer',
            'monthly_fees.*' => 'nullable|numeric',
            'boarding_fees.*' => 'nullable|numeric',
            'management_fees.*' => 'nullable|numeric',
            'exam_fees.*' => 'nullable|numeric',
            'others_fees.*' => 'nullable|numeric',
            'total_fees.*' => 'nullable|numeric',
            'debit.*' => 'nullable|numeric',
            'credit.*' => 'nullable|numeric',
            'transactions_date' => 'nullable|date',
            // 'account_id' => 'exists:accounts,id',
            'class_id' => 'exists:add_classes,id',
            'section_id' => 'exists:add_sections,id',
            'months_id' => 'exists:add_months,id',
            'academic_year_id' => 'exists:add_academies,id',
            // 'created_by_id' => 'exists:users,id',
            'note.*' => 'nullable|string|max:500',
        ]);

        $studentIds = $request->input('student_ids');
        // $fessTypeId = $validatedData['fess_type_id'];
        // $transactionsTypeId = $validatedData['transactions_type_id'];
        $studentBookNumber = $request->input('student_book_number');
        $reciptNo = $request->input('recipt_no');
        $monthlyFees = $request->input('monthly_fees');
        $boardingFees = $request->input('boarding_fees');
        $managementFees = $request->input('management_fees');
        $examFees = $request->input('exam_fees');
        $othersFees = $request->input('others_fees');
        $totalFees = $request->input('total_fees');
        $debit = $request->input('debit');
        $credit = $request->input('credit');
        // $accountId = $validatedData['account_id'];
        $classId = $validatedData['class_id'];
        $sectionId = $validatedData['section_id'];
        $monthsId = $validatedData['months_id'];
        $academicYearId = $validatedData['academic_year_id'];
        // $createdById = $validatedData['created_by_id'];
        $note = $request->input('note');
        $today = Carbon::now();

        foreach ($studentIds as $index => $studentId) {
            // Check if the record already exists
            $existingTransaction = Transactions::where('student_id', $studentId)
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->where('months_id', $monthsId)
                ->where('academic_year_id', $academicYearId)
                ->first();

            if ($existingTransaction) {
                // Update the existing transaction
                $existingTransaction->update([
                    // 'fess_type_id' => $fessTypeId,
                    // 'transactions_type_id' => $transactionsTypeId,
                    'student_book_number' => $studentBookNumber[$index],
                    'recipt_no' => $reciptNo[$index],
                    'monthly_fees' => $monthlyFees[$index],
                    'boarding_fees' => $boardingFees[$index],
                    'management_fees' => $managementFees[$index],
                    'exam_fees' => $examFees[$index],
                    'others_fees' => $othersFees[$index],
                    'total_fees' => $totalFees[$index],
                    'debit' => $debit[$index],
                    'credit' => $credit[$index],
                    'transactions_date' => $today,
                    // 'account_id' => $accountId,
                    // 'created_by_id' => $createdById,
                    'note' => $note[$index],
                ]);
            } else {
                // Create a new transaction if it doesn't exist
                Transactions::create([
                    'student_id' => $studentId,
                    // 'fess_type_id' => $fessTypeId,
                    // 'transactions_type_id' => $transactionsTypeId,
                    'student_book_number' => $studentBookNumber[$index],
                    'recipt_no' => $reciptNo[$index],
                    'monthly_fees' => $monthlyFees[$index],
                    'boarding_fees' => $boardingFees[$index],
                    'management_fees' => $managementFees[$index],
                    'exam_fees' => $examFees[$index],
                    'others_fees' => $othersFees[$index],
                    'total_fees' => $totalFees[$index],
                    'debit' => $debit[$index],
                    'credit' => $credit[$index],
                    'transactions_date' => $today,
                    // 'account_id' => $accountId,
                    'class_id' => $classId,
                    'section_id' => $sectionId,
                    'months_id' => $monthsId,
                    'academic_year_id' => $academicYearId,
                    // 'created_by_id' => $createdById,
                    'note' => $note[$index],
                ]);
            }
        }

        return redirect()->route('add_student_fees.index')->with('success', 'Fees added successfully!');
    }

    public function edit($id)
    {
        // Find the class by ID
        $transactions = Transactions::findOrFail($id);
        $transactionss = Transactions::all();

        // Return view with the class details for editing
        return view('students.add-fees', compact('transactions', 'transactionss'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'student_id' => 'exists:students,id',
            'doner_id' => 'exists:donors,id',
            'lender_id' => 'exists:lenders,id',
            'fess_type_id' => 'exists:add_fess_types,id',
            'transactions_type_id' => 'exists:transactions_types,id',
            'student_book_number' => 'string|max:255',
            'recipt_no' => 'integer',
            'monthly_fees' => 'numeric',
            'boarding_fees' => 'numeric',
            'management_fees' => 'numeric',
            'exam_fees' => 'numeric',
            'total_fees' => 'numeric',
            'debit' => 'numeric',
            'credit' => 'numeric',
            'transactions_date' => 'date',
            'account_id' => 'exists:accounts,id',
            'class_id' => 'exists:add_classes,id',
            'section_id' => 'exists:add_sections,id',
            'months_id' => 'exists:add_months,id',
            'academic_year_id' => 'exists:add_academies,id',
            'created_by_id' => 'exists:users,id',
            'note' => 'string|max:500',
            'isActived' => 'required|boolean', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Find the class by ID
        $transactions = Transactions::findOrFail($id);

        // Update the class with new data
        $transactions->name = $request->name;
        $transactions->isActived = $request->isActived;
        $transactions->save();

        // Redirect to the class list with a success message
        return redirect()->route('add_fees_type.index')->with('success', 'Transaction Type updated successfully!');
    }

    public function destroy($id)
    {
        // Find the class by ID
        $class = Transactions::findOrFail($id);

        // Delete the class
        $class->delete();

        // Redirect back to the list of classes with a success message
        return redirect()->route('add_fees_type.index')->with('success', 'Transaction Type deleted successfully!');
    }


    public function all()
    {

        $transactionss = Transactions::all();
        $years = AddAcademy::all();
        $months = AddMonth::all();
        $classes = AddClass::all();
        $sections = AddSection::all();

        return view('students.add-fees_copy', compact('transactionss', 'years', 'months', 'classes', 'sections', ));

    }

}
