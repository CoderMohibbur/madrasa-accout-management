<?php

namespace App\Http\Controllers;

use App\Models\AddAcademy;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        // Fetch all years
        $student = AddAcademy::all();

        // Return view with the list of years
        return view('students.index', compact('student'));

    }
    public function store(Request $request)
    {
        // Validate the form inputs
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'roll' => 'required|integer',
            'email' => 'required|string|max:255',
            'mobile' => 'required|string|max:255',
            'photo' => 'required|string|max:255',
            'age' => 'required|string|max:255',
            'class' => 'required|string|max:255',
            'year' => 'required|integer',
            'isActived' => 'required|boolean', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database
        AddAcademy::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'full_name' => $validated['full_name'],
            'dob' => $validated['dob'],
            'roll' => $validated['roll'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'photo' => $validated['photo'],
            'age' => $validated['age'],
            'class' => $validated['class'],
            'year' => $validated['year'],
            'isActived' => $validated['isActived'], // Save as boolean: true for 'activate', false for 'deactivate'
        ]);

        // Redirect back or to a success page
        return redirect()->route('add_student.store')->with('success', 'Class added successfully!');
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
