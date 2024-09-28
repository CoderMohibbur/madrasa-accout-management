<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {

        $Sections = Section::all();


        return view('settings.section', compact('Sections'));

    }
    public function store(Request $request)
    {
        // Validate the form inputs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'isActived' => 'required|boolean', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database
        Section::create([
            'name' => $validated['name'],
            'isActived' => $validated['isActived'], // Save as boolean: true for 'activate', false for 'deactivate'
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
            'isActived' => 'required|boolean', // Ensure it's a boolean value
        ]);

        // Find the section by ID
        $Section = Section::findOrFail($id);

        // Update the section with new data
        $Section->name = $request->name;
        $Section->isActived = $request->isActived; // Store the boolean value

        // Save the updated section
        $Section->save();

        // Redirect to the section list with a success message
        return redirect()->route('Section.index')->with('success', 'Section updated successfully!');
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
