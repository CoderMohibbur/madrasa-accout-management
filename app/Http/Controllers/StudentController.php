<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AddClass;
use App\Models\AddAcademy;
use App\Models\AddSection;
use App\Models\AddFessType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        // Validate the incoming request data
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'roll' => 'required|integer',
            'email' => 'nullable|email|max:255',
            'mobile' => 'required|string|max:15',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust as per needs
            'age' => 'required|integer|min:3|max:100',
            'fees_type_id' => 'required|exists:add_fess_types,id',
            'class_id' => 'required|exists:add_classes,id',
            'section_id' => 'required|exists:sections,id',
            'academic_year_id' => 'required|exists:add_academies,id',
            'isActived' => 'boolean',
        ]);

        // Handle the file upload if a photo is provided
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public'); // Store in 'storage/app/public/photos'
        } else {
            $photoPath = null;
        }

        // Create a new student record using the validated data
        Student::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'full_name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'], // Combine first and last name
            'dob' => $validatedData['dob'],
            'roll' => $validatedData['roll'],
            'email' => $validatedData['email'],
            'mobile' => $validatedData['mobile'],
            'photo' => $photoPath, // Save the uploaded photo path
            'age' => $validatedData['age'],
            'fees_type_id' => $validatedData['fees_type_id'],
            'class_id' => $validatedData['class_id'],
            'section_id' => $validatedData['section_id'],
            'academic_year_id' => $validatedData['academic_year_id'],
            'isActived' => $validatedData['isActived'] ?? 1, // Set active by default if not provided
            'isDeleted' => 0, // Set as not deleted by default
        ]);

        // Redirect to the students list with a success message
        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    // Display the specified student
    public function show(Student $student)
    {
        return view('students.show', data: compact('student'));
    }
    public function edit($id)
    {
        // Find the class by ID
        $student = Student::findOrFail($id);
        $classes = AddClass::all(); // Fetch all available classes
        $sections = AddSection::all(); // Fetch all available sections
        $fees_types = AddFessType::all(); // Fetch all available fees types
        $academic_years = AddAcademy::all(); // Fetch all available academic years

        // Return view with the class details for editing
        return view('students.create', compact('student', 'classes', 'sections', 'fees_types', 'academic_years'))->with('success', 'Student updated successfully.');
    }
    public function update(Request $request, Student $student)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'roll' => 'required|integer',
            'email' => 'nullable|email|max:255',
            'mobile' => 'required|string|max:15',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust as per needs
            'age' => 'required|integer|min:3|max:100',
            'fees_type_id' => 'required|exists:add_fess_types,id',
            'class_id' => 'required|exists:add_classes,id',
            'section_id' => 'required|exists:sections,id',
            'academic_year_id' => 'required|exists:add_academies,id',
            'isActived' => 'boolean',
        ]);

        // Handle the file upload if a photo is provided
        if ($request->hasFile('photo')) {
            // Delete the old photo if exists
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo); // Delete old photo from storage
            }
            // Store the new photo
            $photoPath = $request->file('photo')->store('photos', 'public');
        } else {
            $photoPath = $student->photo; // Keep the old photo if no new one is provided
        }

        // Update the student record using the validated data
        $student->update([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'full_name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'], // Combine first and last name
            'dob' => $validatedData['dob'],
            'roll' => $validatedData['roll'],
            'email' => $validatedData['email'],
            'mobile' => $validatedData['mobile'],
            'photo' => $photoPath, // Save the updated photo path
            'age' => $validatedData['age'],
            'fees_type_id' => $validatedData['fees_type_id'],
            'class_id' => $validatedData['class_id'],
            'section_id' => $validatedData['section_id'],
            'academic_year_id' => $validatedData['academic_year_id'],
            'isActived' => $validatedData['isActived'] ?? 1, // Set active by default if not provided
        ]);

        // Redirect to the students list with a success message
        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    // Remove the specified student from storage
    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
}
