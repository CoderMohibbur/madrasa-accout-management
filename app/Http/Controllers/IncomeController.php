<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::query()
            ->notDeleted()
            ->orderBy('name')
            ->paginate(20);

        return view('income.index', compact('incomes'));
    }

    public function create()
    {
        $categories = DB::table('catagories')->orderBy('name')->get();
        return view('income.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'catagory_id'=> 'nullable|exists:catagories,id',
            'isActived'  => 'nullable|boolean',
        ]);

        Income::create([
            'name'       => $data['name'],
            'catagory_id'=> $data['catagory_id'] ?? null,
            'isActived'  => (bool)($data['isActived'] ?? true),
            'isDeleted'  => false,
        ]);

        return redirect()->route('income.index')->with('success', 'Income head created successfully.');
    }

    public function edit(Income $income)
    {
        $categories = DB::table('catagories')->orderBy('name')->get();
        return view('income.edit', compact('income', 'categories'));
    }

    public function update(Request $request, Income $income)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'catagory_id'=> 'nullable|exists:catagories,id',
            'isActived'  => 'nullable|boolean',
        ]);

        $income->update([
            'name'       => $data['name'],
            'catagory_id'=> $data['catagory_id'] ?? null,
            'isActived'  => (bool)($data['isActived'] ?? true),
        ]);

        return redirect()->route('income.index')->with('success', 'Income head updated successfully.');
    }

    public function destroy(Income $income)
    {
        $income->update(['isDeleted' => true]);
        return redirect()->route('income.index')->with('success', 'Income head deleted.');
    }
}
