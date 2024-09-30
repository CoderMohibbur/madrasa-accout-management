<?php

namespace App\Http\Controllers;

use App\Models\AddAcademy;
use Illuminate\Http\Request;

class AddAcademyController extends Controller
{
    public function index()
    {
        // Fetch all years
        $years = AddAcademy::all();

        // Return view with the list of years
        return view('settings.add-academy', compact('years'));

    }
    public function store(Request $request)
    {
        // Validate the form inputs
        $validated = $request->validate([
            'year' => 'required|string|max:255',
            'academic_years' => 'required|string|max:255',
            'starting_date' => 'required|date',
            'ending_date' => 'required|date',
            'isActived' => 'required|boolean', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database
        AddAcademy::create([
            'year' => $validated['year'],
            'academic_years' => $validated['academic_years'],
            'starting_date' => $validated['starting_date'],
            'ending_date' => $validated['ending_date'],
            'isActived' => $validated['isActived'], // Save as boolean: true for 'activate', false for 'deactivate'
        ]);

        // Redirect back or to a success page
        return redirect()->route('add_academy.index')->with('success', 'Class added successfully!');
    }
    public function edit($id)
    {
        // Find the class by ID
        $year = AddAcademy::findOrFail($id);
        $years = AddAcademy::all();

        // Return view with the class details for editing
        return view('settings.add-academy', compact('year', 'years'));
    }
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'year' => 'required|string|max:255',
            'academic_years' => 'required|string|max:255',
            'starting_date' => 'required|date',
            'ending_date' => 'required|date',
            'isActived' => 'required|boolean',
        ]);

        // Find the class by ID
        $year = AddAcademy::findOrFail($id);

        // Update the class with new data
        $year->year = $request->year;
        $year->academic_years = $request->academic_years;
        $year->starting_date = $request->starting_date;
        $year->ending_date = $request->ending_date;
        $year->isActived = $request->isActived;
        $year->save();

        // Redirect to the class list with a success message
        return redirect()->route('add_academy.index')->with('success', 'Class updated successfully!');
    }

    public function destroy($id)
    {
        // Find the class by ID
        $year = AddAcademy::findOrFail($id);

        // Delete the class
        $year->delete();

        // Redirect back to the list of years with a success message
        return redirect()->route('add_academy.index')->with('success', 'Class deleted successfully!');
    }
}
