<?php

namespace App\Http\Controllers;

use App\Models\AddClass;
use Illuminate\Http\Request;
use App\Models\AddRegistrationFess;

class AddRegistrationFessController extends Controller
{
    public function index()
    {
        // Fetch all years
        $registrations = AddRegistrationFess::with('class')->get();
        $classes = AddClass::all(); // Fetch all available class

        // Return view with the list of years
        return view('settings.add-registration-fees', compact('registrations','classes'));

    }
    public function store(Request $request)
    {
        // Validate the form inputs
        $validated = $request->validate([

            'monthly_fee' => 'required|numeric',
            'boarding_fee' => 'required|numeric',
            'management_fee' => 'required|numeric',
            'examination_fee' => 'required|numeric',
            'other' => 'required|numeric',
            'class_id' => 'required|exists:add_classes,id',
            'isActived' => 'required|boolean', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database
        AddRegistrationFess::create([
            'monthly_fee' => $validated['monthly_fee'],
            'boarding_fee' => $validated['boarding_fee'],
            'management_fee' => $validated['management_fee'],
            'examination_fee' => $validated['examination_fee'],
            'class_id' => $validated['class_id'],
            'other' => $validated['other'],

            'isActived' => $validated['isActived'], // Save as boolean: true for 'activate', false for 'deactivate'
        ]);

        // Redirect back or to a success page
        return redirect()->route('add_registration.index')->with('success', 'Registration Fees added successfully!');
    }
    public function edit($id)
    {
        // Find the class by ID
        $registration = AddRegistrationFess::findOrFail($id);
        $registrations = AddRegistrationFess::all();
        $classes = AddClass::all(); // Fetch all available class

        // Return view with the class details for editing
        return view('settings.add-registration-fees', compact('registration', 'registrations','classes'));
    }
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'monthly_fee' => 'required|numeric',
            'boarding_fee' => 'required|numeric',
            'management_fee' => 'required|numeric',
            'examination_fee' => 'required|numeric',
            'other' => 'required|numeric',
            'class_id' => 'required|exists:add_classes,id',
            'isActived' => 'required|boolean',
        ]);

        // Update the student record using the validated data

        $registration = AddRegistrationFess::findOrFail($id);

        $registration->update([
            'monthly_fee' => $request['monthly_fee'],
            'boarding_fee' => $request['boarding_fee'],
            'management_fee' => $request['management_fee'],
            'examination_fee' => $request['examination_fee'],
            'other' => $request['other'],
            'class_id' => $request['class_id'],
            'isActived' => $request['isActived'] ?? 1, // Set active by default if not provided
        ]);

        return redirect()->route('add_registration.index')->with('success', 'Registration Fees updated successfully!');
    }

    public function destroy($id)
    {
        // Find the class by ID
        $registration = AddRegistrationFess::findOrFail($id);

        // Delete the class
        $registration->delete();

        // Redirect back to the list of years with a success message
        return redirect()->route('add_registration.index')->with('success', 'Registration Fees deleted successfully!');
    }
}
