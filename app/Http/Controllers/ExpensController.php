<?php

namespace App\Http\Controllers;

use App\Models\Expen;
use App\Models\Expens;
use Illuminate\Http\Request;

class ExpensController extends Controller
{
    public function index()
    {
        // Fetch all expen
        $expens = Expens::all();

        // Return view with the list of expen
        return view('expens.catagory', compact('expens'));

    }

    public function store(Request $request)
    {
        // Validate the form inputs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'isActived' => 'required|boolean', // Ensure the status is either 'activate' or 'deactivate'
        ]);

        // Create a new class and save to the database
        Expens::create([
            'name' => $validated['name'],
            'isActived' => $validated['isActived'], // Save as boolean: true for 'activate', false for 'deactivate'
        ]);

        // Redirect back or to a success page
        return redirect()->route('add_catagory.index')->with('success', 'expen added successfully!');
    }
    public function edit($id)
    {
        // Find the class by ID
        $expen = Expens::findOrFail($id);
        $expens = Expens::all();

        // Return view with the class details for editing
        return view('expens.catagory', compact('expen', 'expens'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'isActived' => 'required|boolean',
        ]);

        // Find the class by ID
        $expen = Expens::findOrFail($id);

        // Update the class with new data
        $expen->name = $request->name;
        $expen->isActived = $request->isActived;
        $expen->save();

        // Redirect to the class list with a success message
        return redirect()->route('add_catagory.index')->with('success', 'expen updated successfully!');
    }

    public function destroy($id)
    {
        // Find the class by ID
        $expen = Expens::findOrFail($id);

        // Delete the class
        $expen->delete();

        // Redirect back to the list of classes with a success message
        return redirect()->route('add_catagory.index')->with('success', 'expen deleted successfully!');
    }



}
