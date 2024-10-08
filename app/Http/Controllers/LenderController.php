<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lender;
use Illuminate\Http\Request;

class LenderController extends Controller
{
    public function index()
    {
        $lenders = Lender::all();
        $classes = User::all();

        return view('lender.index', compact('lenders', 'classes'));
    }

    public function create()
    {
        $lendes = Lender::all();
        $classes = User::all();

        return view('lender.create', compact('lendes', 'classes'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'address' => 'nullable|string|max:255',
            'bank_detils' => 'nullable|string|max:255',
            'users_id' => 'required|exists:users,id',
            'isActived' => 'boolean',
        ]);

        Lender::create([
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'bank_detils' => $validatedData['bank_detils'],
            'users_id' => $validatedData['users_id'],
            'isActived' => $validatedData['isActived'] ?? 1,
            'isDeleted' => 0,
        ]);

        return redirect()->route('lender.index')->with('success', 'Student created successfully.');
    }

    public function show(Lender $lender)
    {
        return view('lender.show', compact('lender'));
    }

    public function edit($id)
    {
        $lender = Lender::findOrFail($id);
        $classes = User::all();

        return view('lender.create', compact('lender', 'classes'));
    }

    public function update(Request $request, Lender $lender)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'address' => 'nullable|string|max:255',
            'bank_detils' => 'nullable|string|max:255',
            'users_id' => 'required|exists:users,id',
            'isActived' => 'boolean',
        ]);

        $lender->update([
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'bank_detils' => $validatedData['bank_detils'],
            'users_id' => $validatedData['users_id'],
            'isActived' => $validatedData['isActived'] ?? 1,
            'isDeleted' => 0,
        ]);

        return redirect()->route('lender.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Lender $lender)
    {
        $lender->delete();
        return redirect()->route('lender.index')->with('success', 'Student deleted successfully.');
    }
}
