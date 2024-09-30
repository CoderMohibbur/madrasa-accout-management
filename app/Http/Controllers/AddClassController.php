<?php

namespace App\Http\Controllers;

use App\Models\AddClass;
use Illuminate\Http\Request;

class AddClassController extends Controller
{
    public function index()
    {
        // Fetch all classes
        $classes = AddClass::all();

        // Return view with the list of classes
        return view('settings.add-class', compact('classes'));

    }

    public function store(Request $request)
    {
        // Validate the form inputs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'isActived' => 'required|boolean', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database
        AddClass::create([
            'name' => $validated['name'],
            'isActived' => $validated['isActived'], // Save as boolean: true for 'activate', false for 'deactivate'
        ]);

        // Redirect back or to a success page
        return redirect()->route('add_class.index')->with('success', 'Class added successfully!');
    }
    public function edit($id)
    {
        // Find the class by ID
        $class = AddClass::findOrFail($id);
        $classes = AddClass::all();

        // Return view with the class details for editing
        return view('settings.add-class', compact('class', 'classes'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'isActived' => 'required|boolean',
        ]);

        // Find the class by ID
        $class = AddClass::findOrFail($id);

        // Update the class with new data
        $class->name = $request->name;
        $class->isActived = $request->isActived;
        $class->save();

        // Redirect to the class list with a success message
        return redirect()->route('add_class.index')->with('success', 'Class updated successfully!');
    }

    public function destroy($id)
    {
        // Find the class by ID
        $class = AddClass::findOrFail($id);

        // Delete the class
        $class->delete();

        // Redirect back to the list of classes with a success message
        return redirect()->route('add_class.index')->with('success', 'Class deleted successfully!');
    }
}
