<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lender;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LenderController extends Controller
{
    public function index()
    {
        $lenders = Lender::all();
        $classes = User::all();

        return view('lender.index', compact('lenders', 'classes'));
    }

    public function create()
    {
        $lendes = Lender::all();
        $classes = User::all();

        return view('lender.create', compact('lendes', 'classes'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'address' => 'nullable|string|max:255',
            'bank_detils' => 'nullable|string|max:255',
            'users_id' => 'required|exists:users,id',
            'isActived' => 'boolean',
        ]);

        Lender::create([
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'bank_detils' => $validatedData['bank_detils'],
            'users_id' => $validatedData['users_id'],
            'isActived' => $validatedData['isActived'] ?? 1,
            'isDeleted' => 0,
        ]);

        return redirect()->route('lender.index')->with('success', 'Student created successfully.');
    }

    public function show(Lender $lender)
    {
        return view('lender.show', compact('lender'));
    }

    public function edit($id)
    {
        $lender = Lender::findOrFail($id);
        $classes = User::all();

        return view('lender.create', compact('lender', 'classes'));
    }

    public function update(Request $request, Lender $lender)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'address' => 'nullable|string|max:255',
            'bank_detils' => 'nullable|string|max:255',
            'users_id' => 'required|exists:users,id',
            'isActived' => 'boolean',
        ]);

        $lender->update([
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'bank_detils' => $validatedData['bank_detils'],
            'users_id' => $validatedData['users_id'],
            'isActived' => $validatedData['isActived'] ?? 1,
            'isDeleted' => 0,
        ]);

        return redirect()->route('lender.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Lender $lender)
    {
        $lender->delete();
        return redirect()->route('lender.index')->with('success', 'Student deleted successfully.');
    }

    public function add_loan()
    {

        $lenders = Lender::all();

        return view('lender.add-loan', compact('lenders'));
    }

    public function lonan_store(Request $request)
    {
        $today = Carbon::now();
        // Validate the form inputs
        $validatedData = $request->validate([
            'lender_id' => 'exists:lenders,id',
            'total_fees' => 'numeric',
            'note' => 'string|max:500',
            'isActived' => 'required|boolean', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database

        Transactions::create([
            'lender_id' => $validatedData['lender_id'],
            'fess_type_id' => 1,
            'transactions_type_id' => 1,
            'total_fees' => $validatedData['total_fees'],
            'transactions_date' => $today,
            'note' => $validatedData['note'],
            'isActived' => $validatedData['isActived'] ?? 1, // Save as boolean: true for 'activate', false for 'deactivate'
        ]);

        // Redirect back or to a success page
        return redirect()->route('add_loan')->with('success', 'Class added successfully!');
    }
}
