<?php

namespace App\Http\Controllers;

use App\Models\AddFessType;
use Illuminate\Http\Request;

class AddFessTypeController extends Controller
{
    public function index()
    {
        // Fetch all classes
        $classes = AddFessType::all();

        // Return view with the list of classes
        return view('settings.add-fees-type', compact('classes'));

    }

    public function store(Request $request)
    {
        // Validate the form inputs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'isActived' => 'required|boolean', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database
        AddFessType::create([
            'name' => $validated['name'],
            'isActived' => $validated['isActived'], // Save as boolean: true for 'activate', false for 'deactivate'
        ]);

        // Redirect back or to a success page
        return redirect()->route('add_fees_type.index')->with('success', 'Fees Type added successfully!');
    }
    public function edit($id)
    {
        // Find the class by ID
        $class = AddFessType::findOrFail($id);
        $classes = AddFessType::all();

        // Return view with the class details for editing
        return view('settings.add-fees-type', compact('class', 'classes'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'isActived' => 'required|boolean',
        ]);

        // Find the class by ID
        $class = AddFessType::findOrFail($id);

        // Update the class with new data
        $class->name = $request->name;
        $class->isActived = $request->isActived;
        $class->save();

        // Redirect to the class list with a success message
        return redirect()->route('add_fees_type.index')->with('success', 'Fees Type updated successfully!');
    }

    public function destroy($id)
    {
        // Find the class by ID
        $class = AddFessType::findOrFail($id);

        // Delete the class
        $class->delete();

        // Redirect back to the list of classes with a success message
        return redirect()->route('add_fees_type.index')->with('success', 'Fees Type deleted successfully!');
    }
}
