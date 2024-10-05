<?php

namespace App\Http\Controllers;

use App\Models\income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {
        // Fetch all years
        $Incomes = income::all();

        // Return view with the list of years
        return view('income.index', compact('Incomes'));

    }
    // Show the form for creating a new student
    public function create()
    {

        $Incomes = income::all(); // Fetch all available fees types

        return view('income.create', compact('Incomes'));
    }
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'isActived' => 'boolean',
        ]);


        // Create a new student record using the validated data
        income::create([
            'name' => $validatedData['name'],
            'isActived' => $validatedData['isActived'] ?? 1, // Set active by default if not provided
            'isDeleted' => 0, // Set as not deleted by default
        ]);

        // Redirect to the students list with a success message
        return redirect()->route('income.index')->with('success', 'income created successfully.');
    }

    // Display the specified student
    public function show(income $Income)
    {
        return view('income.create', data: compact( 'Income'));
    }
    public function edit($id)
    {
        // Find the class by ID
        $Income = income::findOrFail($id);
        $Incomes = income::all();

        // Return view with the class details for editing



     return view('income.create', compact('Income'));

    }
    public function update(Request $request, income $Income)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'isActived' => 'boolean',
        ]);


        // Update the student record using the validated data
        $Income->update([
            'name' => $validatedData['name'],
            'isActived' => $validatedData['isActived'] ?? 1, // Set active by default if not provided
        ]);

        // Redirect to the students list with a success message
        return redirect()->route('income.index')->with('success', 'income updated successfully.');
    }

    // Remove the specified student from storage
    public function destroy(income $income)
    {
        $income->delete();
        return redirect()->route('income.index')->with('success', 'income deleted successfully.');
    }


}
