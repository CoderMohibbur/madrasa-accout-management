<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transactions;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(){

        $accounts =Account ::all();

        return view('account.index', compact('accounts'));
    }

    public function create(){

        return view('account.create');
    }
    public function store(Request $request){

       // Validate the incoming request data
        $validatedData = $request->validate(rules: [
            'name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_details' => 'required|string|max:15',
            'opening_balance' => 'required|numeric',
            'current_balance' => 'required|numeric',
            'isActived' => 'boolean',
        ]);


        // Create a new student record using the validated data
        Account::create([
            'name' => $validatedData['name'],
            'account_number' => $validatedData['account_number'],
            'account_details' => $validatedData['account_details'],
            'opening_balance' => $validatedData['opening_balance'],
            'current_balance' => $validatedData['current_balance'],
            'isActived' => $validatedData['isActived'] ?? 1, // Set active by default if not provided
            'isDeleted' => 0, // Set as not deleted by default
        ]);

        // Redirect to the students list with a success message
        return redirect()->route('account.index')->with('success', 'account created successfully.');



    }

    public function show(Account $account)
    {
        return view('account.show', data: compact('account'));
    }
    public function edit($id)
    {
       // Find the class by ID
       $account = Account::findOrFail($id);
       $accounts = Account::all();

        // Return view with the class details for editing
        return view('account.create', compact('account','accounts'))->with('success', 'Student updated successfully.');
    }
    public function update(Request $request, Account $account)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_details' => 'required|string|max:15',
            'opening_balance' => 'required|numeric',
            'current_balance' => 'required|numeric',
            'isActived' => 'boolean',
        ]);



        // Update the student record using the validated data
        $account->update([
           'name' => $validatedData['name'],
            'account_number' => $validatedData['account_number'],
            'account_details' => $validatedData['account_details'],
            'opening_balance' => $validatedData['opening_balance'],
            'current_balance' => $validatedData['current_balance'],
            'isActived' => $validatedData['isActived'] ?? 1, // Set active by default if not provided
            'isDeleted' => 0, // Set as not deleted by default
        ]);

        // Redirect to the students list with a success message
        return redirect()->route('account.index')->with('success', 'account updated successfully.');
    }
    public function destroy($id)
    {
        // Find the class by ID
        $account = Account::findOrFail($id);

        // Delete the class
        $account->delete();

        // Redirect back to the list of classes with a success message
        return redirect()->route('account.index')->with('success', 'account deleted successfully!');
    }
    public function account(){

        $transactionss = Transactions::all();


        return view('account.chart-of-accounts', compact('transactionss'));
    }

}
