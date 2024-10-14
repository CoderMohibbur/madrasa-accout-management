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
        $users=User::all();

        // Return view with the list of classes
        return view('students.add-fees', compact('transactionss','students','accounts','classes','sections','years','months','transactionss','users'));

    }

    public function getStudents(Request $request)
    {
        $students = Student::where('academic_year_id', $request->academic_year_id)
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->get();

        return view('students.partials.students_list', compact('students'));
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
            'fess_type_id' =>$validatedData['fess_type_id'],
            'transactions_type_id' => $validatedData['transactions_type_id'],
            'student_book_number' => $validatedData['student_book_number'],
            'recipt_no' => $validatedData['recipt_no'],
            'monthly_fees' => $validatedData['monthly_fees'],
            'boarding_fees' =>$validatedData['boarding_fees'],
            'management_fees' => $validatedData['management_fees'],
            'exam_fees' => $validatedData['exam_fees'],
            'others_fees' =>$validatedData['others_fees'],
            'total_fees' => $validatedData['total_fees'],
            'debit' => $validatedData['debit'],
            'credit' => $validatedData['credit'],
            'transactions_date' => $validatedData['transactions_date'],
            'account_id' => $validatedData['account_id'],
            'class_id' => $validatedData['class_id'],
            'section_id' => $validatedData['section_id'],
            'months_id' => $validatedData['months_id'],
            'academic_year_id' =>$validatedData['academic_year_id'],
            'created_by_id' =>$validatedData['created_by_id'],
            'note' => $validatedData['note'],
            'isActived' => $validatedData['isActived'] ?? 1, // Save as boolean: true for 'activate', false for 'deactivate'
        ]);

        // Redirect back or to a success page
        return redirect()->route('add_fees_type.index')->with('success', 'Class added successfully!');
    }

    public function bulkStore(Request $request)
    {
        $studentIds = $request->input('student_ids');
        $monthlyFees = $request->input('monthly_fees');
        $boardingFees = $request->input('boarding_fees');
        // আরও ফিল্ড গুলো নিবেন

        foreach ($studentIds as $index => $studentId) {
            Transactions::create([
                'student_id' => $studentId,
                'monthly_fees' => $monthlyFees[$index],
                'boarding_fees' => $boardingFees[$index],
                // অন্যান্য ফিল্ড গুলো যোগ করুন
            ]);
        }

        return redirect()->route('fees.index')->with('success', 'Fees added successfully!');
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
            '
            ' => 'numeric',
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
        return redirect()->route('add_fees_type.index')->with('success', 'Class updated successfully!');
    }

    public function destroy($id)
    {
        // Find the class by ID
        $class = Transactions::findOrFail($id);

        // Delete the class
        $class->delete();

        // Redirect back to the list of classes with a success message
        return redirect()->route('add_fees_type.index')->with('success', 'Class deleted successfully!');
    }

}

