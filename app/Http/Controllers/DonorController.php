<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Donor;
use App\Models\Account;
use App\Models\AddFessType;
use App\Models\Transactions;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all years
        $Donors = Donor::all();

        // Return view with the list of years
        return view('donors.index', compact('Donors'));
    }
    // Show the form for creating a new student
    public function create()
    {

        $fees_types = AddFessType::all(); // Fetch all available fees types

        return view('donors.create', compact('fees_types'));
    }
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:15',
            'fees_type_id' => 'required|exists:add_fess_types,id',
            'isActived' => 'boolean',
        ]);


        // Create a new student record using the validated data
        Donor::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'mobile' => $validatedData['mobile'],
            'fees_type_id' => $validatedData['fees_type_id'],
            'isActived' => $validatedData['isActived'] ?? 1, // Set active by default if not provided
            'isDeleted' => 0, // Set as not deleted by default
        ]);

        // Redirect to the students list with a success message
        return redirect()->route('donors.index')->with('success', 'Student created successfully.');
    }

    // Display the specified student
    public function show(Donor $Donor)
    {
        return view('Donors.show', data: compact(var_name: 'Donors'));
    }
    public function edit($id)
    {
        // Find the class by ID
        $Donor = Donor::findOrFail($id);
        $fees_types = AddFessType::all();

        // Return view with the class details for editing

        return view('donors.create', compact('Donor', 'fees_types'));
    }
    public function update(Request $request, Donor $Donor)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:15',
            'fees_type_id' => 'required|exists:add_fess_types,id',
            'isActived' => 'boolean',
        ]);


        // Update the student record using the validated data
        $Donor->update([
            'name' => $validatedData['name'],
            'mobile' => $validatedData['mobile'],
            'email' => $validatedData['email'],
            'fees_type_id' => $validatedData['fees_type_id'],
            'isActived' => $validatedData['isActived'] ?? 1, // Set active by default if not provided
        ]);

        // Redirect to the students list with a success message
        return redirect()->route('donors.index')->with('success', 'Student updated successfully.');
    }

    // Remove the specified student from storage
    public function destroy(Donor $Donor)
    {
        $Donor->delete();
        return redirect()->route('donors.index')->with('success', 'Student deleted successfully.');
    }
    public function donars()
    {
        $transactionss = Transactions::whereNotNull('doner_id')->get();
        $Donors = Donor::all();
        $accounts = Account::all();

        return view('donors.add-donar', compact('transactionss', 'Donors', 'accounts'));
    }

    public function donosr_store(Request $request)
    {
        $today = Carbon::now();
        // Validate the form inputs
        $validatedData = $request->validate([
            'doner_id' => 'exists:donors,id',
            'total_fees' => 'numeric',
            'c_s_1' => 'string|max:250',


            'account_id' => 'exists:accounts,id',
            'note' => 'string|max:500',
            'isActived' => 'required|boolean', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database

        Transactions::create([
            'doner_id' => $validatedData['doner_id'],
            'fess_type_id' => 1,
            'transactions_type_id' => 1,
            'total_fees' => $validatedData['total_fees'],
            'c_s_1' => $validatedData['c_s_1'],

            'account_id' => $validatedData['account_id'],

            'transactions_date' => $today,
            'note' => $validatedData['note'],
            'isActived' => $validatedData['isActived'] ?? 1, // Save as boolean: true for 'activate', false for 'deactivate'
        ]);

        // Redirect back or to a success page
        return redirect()->route('add_donar')->with('success', 'Class added successfully!');
    }

    public function donors_loan(Donor $lender)
    {
        $transactionss = Transactions::with('doner')->whereNotNull('doner_id')->get();
        $Donors = Donor::all();
        $accounts = Account::all();

        return view('lender.add-loan', compact('transactionss', 'Donors', 'accounts'));
    }

    public function edit_donor($id)
    {
        $transaction = Transactions::findOrFail($id);
        $transactionss = Transactions::with('doner')->whereNotNull('doner_id')->get();
        $Donors = Donor::all();
        $accounts = Account::all();

        return view('donors.add-donar', compact('transaction', 'Donors', 'accounts','transactionss'));
    }

    public function update_donor(Request $request, $id)
    {
        $validatedData = $request->validate([
            'doner_id' => 'doner_id',
            'total_fees' => 'numeric',
            'account_id' => 'exists:accounts,id',
            'c_s_1' => 'string|max:250',
            'note' => 'nullable|string|max:500',
            'isActived' => 'required|boolean',
        ]);

        $today = Carbon::now();
        $transaction = Transactions::findOrFail($id);

        // Update the transaction
        $transaction->update([
            'doner_id' => $validatedData['doner_id'],
            'fess_type_id' => 1,
            'transactions_type_id' => 1,
            'total_fees' => $validatedData['total_fees'],
            'account_id' => $validatedData['account_id'],
            'c_s_1' => $validatedData['c_s_1'],


            'transactions_date' => $today,
            'note' => $validatedData['note'],
            'isActived' => $validatedData['isActived'],
        ]);

        return redirect()->route('add_donar')->with('success', 'Loan updated successfully.');
    }

    public function destroy_donor($id)
    {
        $Donor = Donor::findOrFail($id);

        $Donor->delete();
        return redirect()->route('add_donar')->with('success', 'Lender deleted successfully.');
    }
}
