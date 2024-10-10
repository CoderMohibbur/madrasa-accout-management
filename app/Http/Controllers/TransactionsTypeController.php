<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionsType;

class TransactionsTypeController extends Controller
{
    public function index()
    {
        // Fetch all classes
        $transactions = TransactionsType::all();

        // Return view with the list of classes
        return view('settings.add-transactions-type', compact('transactions'));

    }

    public function store(Request $request)
    {
        // Validate the form inputs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'isActived' => 'required|boolean', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database
        TransactionsType::create([
            'name' => $validated['name'],
            'isActived' => $validated['isActived'], // Save as boolean: true for 'activate', false for 'deactivate'
        ]);

        // Redirect back or to a success page
        return redirect()->route('add_transaction_type.index')->with('success', 'Class added successfully!');
    }

    public function edit($id)
    {
        // Find the class by ID
        $transaction = TransactionsType::findOrFail($id);
        $transactions = TransactionsType::all();

        // Return view with the class details for editing
        return view('settings.add-transactions-type', compact('transaction', 'transactions'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'isActived' => 'required|boolean',
        ]);

        // Find the class by ID
        $transaction = TransactionsType::findOrFail($id);

        // Update the class with new data
        $transaction->name = $request->name;
        $transaction->isActived = $request->isActived;
        $transaction->save();

        // Redirect to the class list with a success message
        return redirect()->route('add_transaction_type.index')->with('success', 'Class updated successfully!');
    }

    public function destroy($id)
    {
        // Find the class by ID
        $transaction = TransactionsType::findOrFail($id);

        // Delete the class
        $transaction->delete();

        // Redirect back to the list of classes with a success message
        return redirect()->route('add_transaction_type.index')->with('success', 'Class deleted successfully!');
    }
}
