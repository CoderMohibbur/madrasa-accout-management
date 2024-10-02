<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\AddFessType;
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
        return view('Donors.show', data: compact('Donors'));
    }
    public function edit($id)
    {
        // Find the class by ID
        $Donor = Donor::findOrFail($id);
        $fees_types = AddFessType::all();

        // Return view with the class details for editing

        return view('donors.create', compact('Donors', 'Donor'));

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
}

