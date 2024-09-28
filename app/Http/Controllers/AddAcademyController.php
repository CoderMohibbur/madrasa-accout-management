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
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database
        AddAcademy::create([
            'name' => $validated['name'],
            'status' => $validated['status'], // Save as boolean: true for 'activate', false for 'deactivate'
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
            'status' => 'required|in:active,inactive',
        ]);

        // Find the class by ID
        $year = AddAcademy::findOrFail($id);

        // Update the class with new data
        $year->year = $request->year;
        $year->status = $request->statusy;
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
