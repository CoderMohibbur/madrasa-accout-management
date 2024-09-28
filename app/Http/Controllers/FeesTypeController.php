<?php

namespace App\Http\Controllers;

use App\Models\FeesType;
use Illuminate\Http\Request;

class FeesTypeController extends Controller
{
    public function index()
    {
        // Fetch all classes
        $classes = FeesType::all();

        // Return view with the list of classes
        return view('settings.add-fees-type', compact('classes'));

    }

    public function store(Request $request)
    {
        // Validate the form inputs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database
        FeesType::create([
            'name' => $validated['name'],
            'status' => $validated['status'], // Save as boolean: true for 'activate', false for 'deactivate'
        ]);

        // Redirect back or to a success page
        return redirect()->route('add_fees_type.index')->with('success', 'Class added successfully!');
    }
    public function edit($id)
    {
        // Find the class by ID
        $class = FeesType::findOrFail($id);
        $classes = FeesType::all();

        // Return view with the class details for editing
        return view('settings.add-fees-type', compact('class', 'classes'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        // Find the class by ID
        $class = FeesType::findOrFail($id);

        // Update the class with new data
        $class->name = $request->name;
        $class->status = $request->status;
        $class->save();

        // Redirect to the class list with a success message
        return redirect()->route('add_fees_type.index')->with('success', 'Class updated successfully!');
    }

    public function destroy($id)
    {
        // Find the class by ID
        $class = FeesType::findOrFail($id);

        // Delete the class
        $class->delete();

        // Redirect back to the list of classes with a success message
        return redirect()->route('add_fees_type.index')->with('success', 'Class deleted successfully!');
    }
}