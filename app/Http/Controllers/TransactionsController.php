<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    //
    public function index()
    {
        // Fetch all classes
        $transactionss = Transactions::all();

        // Return view with the list of classes
        return view('students.add-fees', compact('transactionss'));

    }

    public function store(Request $request)
    {
        // Validate the form inputs
        $validated = $request->validate([


            'note' => 'string|max:255',
            'recipt_no' => 'integer',
            'monthly_fees' => 'numeric',
            'scholarship_amount' => 'numeric',
            'boarding_fees' => 'numeric',
            'management_fees' => 'numeric',
            'exam_fees' => 'numeric',
            'others_fees' => 'numeric',
            'total_fees' => 'numeric',
            'debit' => 'numeric',
            'credit' => 'numeric',
            'transactions_date' => 'date',
            'student_id' => 'exists:students,id',
            'doner_id' => 'exists:donors,id',
            'lender_id' => 'exists:lenders,id',
            'fees_type_id' => 'exists:add_fess_types,id',
            'section_id' => 'exists:add_sections,id',
            'academic_year_id' => 'exists:add_academies,id',
            'account_id' => 'exists:accounts,id',
            'class_id' => 'exists:add_classes,id',
            'months_id' => 'exists:add_months,id',
            'created_by_id' => 'exists:users,id',
            'isActived' => 'required|boolean', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database
        Transactions::create([
            'noth' => $validated['noth'],
            'recipt_no' => $validated['recipt_no'],
            'monthly_fees' => $validated['monthly_fees'],
            'scholarship_amount' => $validated['scholarship_amount'],
            'boarding_fees' => $validated['boarding_fees'],
            'management_fees' => $validated['management_fees'],
            'exam_fees' => $validated['exam_fees'],
            'others_fees' => $validated['others_fees'],
            'total_fees' => $validated['total_fees'],
            'debit' => $validated['debit'],
            'credit' => $validated['credit'],
            'transactions_date' => $validated['transactions_date'],
            'student_id' => $validated['student_id'],
            'doner_id' => $validated['doner_id'],
            'lender_id' => $validated['lender_id'],
            'fees_type_id' => $validated['fees_type_id'],
            'section_id' => $validated['section_id'],
            'academic_year_id' => $validated['academic_year_id'],
            'account_id' => $validated['account_id'],
            'class_id' => $validated['class_id'],
            'months_id' => $validated['months_id'],
            'created_by_id' => $validated['created_by_id'],

            'isActived' => $validated['isActived'], // Save as boolean: true for 'activate', false for 'deactivate'
        ]);
        Transactions::create([
            'student_id' => 1,
            'doner_id' => NULL,
            'lender_id' => NULL,
            'fess_type_id' => 1,
            'transactions_type_id' => 1,
            'student_book_number' => 'test',
            'recipt_no' => 125425,
            'monthly_fees' => 12424,
            'boarding_fees' => 12424,
            'management_fees' => 12424,
            'exam_fees' => 12424,
            'others_fees' => 12424,
            'total_fees' => 12424,
            'debit' => 12424,
            'credit' => 12424,
            'transactions_date' => '2024-10-09',
            'account_id' => 1,
            'class_id' => 1,
            'section_id' => 1,
            'months_id' => 1,
            'academic_year_id' => 1,
            'created_by_id' => 1,
            'note' => 'test',
            'isActived' => 1, // Save as boolean: true for 'activate', false for 'deactivate'
        ]);

        // Redirect back or to a success page
        return redirect()->route('add_fees_type.index')->with('success', 'Class added successfully!');
    }
    public function edit($id)
    {
        // Find the class by ID
        $class = Transactions::findOrFail($id);
        $classes = Transactions::all();

        // Return view with the class details for editing
        return view('settings.add-fees-type', compact('class', 'classes'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'isActived' => 'required|boolean',
        ]);

        // Find the class by ID
        $class = Transactions::findOrFail($id);

        // Update the class with new data
        $class->name = $request->name;
        $class->isActived = $request->isActived;
        $class->save();

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

