<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AddClass;
use App\Models\AddAcademy;
use App\Models\AddSection;
use App\Models\AddFessType;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        // Fetch all years
        $students = Student::all();

        // Return view with the list of years
        return view('students.index', compact('students'));

    }
    // Show the form for creating a new student
    public function create()
    {
        // Retrieve all necessary data from the database for the form
        $classes = AddClass::all(); // Fetch all available classes
        $sections = AddSection::all(); // Fetch all available sections
        $fees_types = AddFessType::all(); // Fetch all available fees types
        $academic_years = AddAcademy::all(); // Fetch all available academic years

        // Pass the data to the view
        return view('students.create', compact('classes', 'sections', 'fees_types', 'academic_years'));
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
            'photo' => 'string|max:255',
            'age' => 'required|string|max:255',
            'class' => 'required|string|max:255',
            'year' => 'required|integer',
            'isActived' => 'required|boolean', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database
        Student::create([
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
        return redirect()->route('add_student.index')->with('success', 'Class added successfully!');
    }
    // Display the specified student
    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }
    public function edit($id)
    {
        // Find the class by ID
        $year = Student::findOrFail($id);
        $years = Student::all();

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
        $year = Student::findOrFail($id);

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
    // Remove the specified student from storage
    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
}
