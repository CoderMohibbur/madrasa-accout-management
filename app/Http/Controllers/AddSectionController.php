<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\AddSection;
use Illuminate\Http\Request;

class AddSectionController extends Controller
{
    public function index()
    {

        $Sections = AddSection::all();


        return view('settings.add-section', compact('Sections'));

    }
    public function store(Request $request)
    {
        // Validate the form inputs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'isActived' => 'required|boolean', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database
        AddSection::create([
            'name' => $validated['name'],
            'isActived' => $validated['isActived'], // Save as boolean: true for 'activate', false for 'deactivate'
        ]);

        // Redirect back or to a success page
        return redirect()->route('Section.index')->with('success', 'Section added successfully!');
    }
    public function edit($id)
    {
        // Find the Section by ID
        $Section = AddSection::findOrFail($id);
        $Sections = AddSection::all();

        // Return view with the Section details for editing
        return view('settings.add-section', compact('Section', 'Sections'));
    }
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'isActived' => 'required|boolean', // Ensure it's a boolean value
        ]);

        // Find the section by ID
        $Section = AddSection::findOrFail($id);

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
        $Section = AddSection::findOrFail($id);

        // Delete the class
        $Section->delete();

        // Redirect back to the list of classes with a success message
        return redirect()->route('Section.index')->with('success', 'Section deleted successfully!');
    }

}
