<?php

namespace App\Http\Controllers;

use App\Models\Expens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpensController extends Controller
{
    public function index()
    {
        $expenses = Expens::query()
            ->notDeleted()
            ->orderBy('name')
            ->paginate(20);

        return view('expens.index', compact('expenses'));
    }

    public function create()
    {
        $categories = DB::table('catagories')->orderBy('name')->get();
        return view('expens.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'catagory_id'=> 'nullable|exists:catagories,id',
            'isActived'  => 'nullable|boolean',
        ]);

        Expens::create([
            'name'       => $data['name'],
            'catagory_id'=> $data['catagory_id'] ?? null,
            'isActived'  => (bool)($data['isActived'] ?? true),
            'isDeleted'  => false,
        ]);

        return redirect()->route('expens.index')->with('success', 'Expense head created successfully.');
    }

    public function edit(Expens $expen)
    {
        $categories = DB::table('catagories')->orderBy('name')->get();
        return view('expens.edit', ['expense' => $expen, 'categories' => $categories]);
    }

    public function update(Request $request, Expens $expen)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'catagory_id'=> 'nullable|exists:catagories,id',
            'isActived'  => 'nullable|boolean',
        ]);

        $expen->update([
            'name'       => $data['name'],
            'catagory_id'=> $data['catagory_id'] ?? null,
            'isActived'  => (bool)($data['isActived'] ?? true),
        ]);

        return redirect()->route('expens.index')->with('success', 'Expense head updated successfully.');
    }

    // Soft delete style (isDeleted=true)
    public function destroy(Expens $expen)
    {
        $expen->update(['isDeleted' => true]);
        return redirect()->route('expens.index')->with('success', 'Expense head deleted.');
    }
}
