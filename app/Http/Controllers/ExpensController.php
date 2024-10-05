<?php

namespace App\Http\Controllers;

use App\Models\Expen;
use App\Models\Expens;
use Illuminate\Http\Request;

class ExpensController extends Controller
{
    public function index()
    {
        // Fetch all years
        $Expens = Expens::all();

        // Return view with the list of years
        return view('expens.index', compact('Expens'));

    }
    // Show the form for creating a new student
    public function create()
    {

        $Expens = Expens::all(); // Fetch all available fees types

        return view('expens.create', compact('Expens'));
    }
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'isActived' => 'boolean',
        ]);


        // Create a new student record using the validated data
        Expens::create([
            'name' => $validatedData['name'],
            'isActived' => $validatedData['isActived'] ?? 1, // Set active by default if not provided
            'isDeleted' => 0, // Set as not deleted by default
        ]);

        // Redirect to the students list with a success message
        return redirect()->route('expens.index')->with('success', 'Student created successfully.');
    }

    // Display the specified student
    public function show(Expens $Expen)
    {
        return view('expens.create', data: compact( 'Expens'));
    }
    public function edit($id)
    {
        // Find the class by ID
        $Expen = Expens::findOrFail($id);
        $Expens = Expens::all();

        // Return view with the class details for editing



     return view('expens.create', compact('Expen'));

    }
    public function update(Request $request, Expens $Expen)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'isActived' => 'boolean',
        ]);


        // Update the student record using the validated data
        $Expen->update([
            'name' => $validatedData['name'],
            'isActived' => $validatedData['isActived'] ?? 1, // Set active by default if not provided
        ]);

        // Redirect to the students list with a success message
        return redirect()->route('expens.index')->with('success', 'Student updated successfully.');
    }

    // Remove the specified student from storage
    public function destroy(Expens $Expen)
    {
        $Expen->delete();
        return redirect()->route('expens.index')->with('success', 'Student deleted successfully.');
    }



}
