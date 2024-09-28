<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index(){

        $Sections = Section::all();


        return view('settings.section',compact('Sections'));

    }
    public function store(Request $request)
    {
        // Validate the form inputs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database
        Section::create([
            'name' => $validated['name'],
            'status' => $validated['status'], // Save as boolean: true for 'activate', false for 'deactivate'
        ]);

        // Redirect back or to a success page
        return redirect()->route('Section.index')->with('success', 'Class added successfully!');
    }
    public function edit($id)
    {
        // Find the Section by ID
        $Section = Section::findOrFail($id);
        $Sections = Section::all();

        // Return view with the Section details for editing
        return view('settings.section', compact('Section', 'Sections'));
    }
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        // Find the class by ID
        $Section = Section::findOrFail($id);

        // Update the class with new data
        $Section->name = $request->name;
        $Section->status = $request->status;
        $Section->save();

        // Redirect to the class list with a success message
        return redirect()->route('Section.index')->with('success', 'Class updated successfully!');
    }
    public function destroy($id)
    {
        // Find the class by ID
        $Section = Section::findOrFail($id);

        // Delete the class
        $Section->delete();

        // Redirect back to the list of classes with a success message
        return redirect()->route('add_month.index')->with('success', 'Class deleted successfully!');
    }
   
}
