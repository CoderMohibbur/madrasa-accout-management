<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transactions;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::latest()->paginate(30);
        return view('account.index', compact('accounts'));
    }

    public function create()
    {
        return view('account.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_details' => 'required|string|max:15',
            'opening_balance' => 'required|numeric',
            'current_balance' => 'required|numeric',
            'isActived' => 'sometimes|boolean',
        ]);

        Account::create([
            'name' => $validatedData['name'],
            'account_number' => $validatedData['account_number'],
            'account_details' => $validatedData['account_details'],
            'opening_balance' => $validatedData['opening_balance'],
            'current_balance' => $validatedData['current_balance'],
            'isActived' => $validatedData['isActived'] ?? 1,
            'isDeleted' => 0,
        ]);

        return redirect()->route('account.index')->with('success', 'Account created successfully.');
    }

    public function show(Account $account)
    {
        return view('account.show', compact('account'));
    }

    public function edit($id)
    {
        $account = Account::findOrFail($id);
        $accounts = Account::all();

        // NOTE: edit view যদি create.blade.php reuse করে
        return view('account.create', compact('account', 'accounts'));
    }

    public function update(Request $request, Account $account)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_details' => 'required|string|max:15',
            'opening_balance' => 'required|numeric',
            'current_balance' => 'required|numeric',
            'isActived' => 'sometimes|boolean',
        ]);

        $account->update([
            'name' => $validatedData['name'],
            'account_number' => $validatedData['account_number'],
            'account_details' => $validatedData['account_details'],
            'opening_balance' => $validatedData['opening_balance'],
            'current_balance' => $validatedData['current_balance'],
            'isActived' => $validatedData['isActived'] ?? 1,
            'isDeleted' => 0,
        ]);

        return redirect()->route('account.index')->with('success', 'Account updated successfully.');
    }

    public function destroy($id)
    {
        $account = Account::findOrFail($id);
        $account->delete();

        return redirect()->route('account.index')->with('success', 'Account deleted successfully!');
    }

    /**
     * ✅ Chart of Accounts page
     * - এখানে Accounts দেখাবে
     * - debit/credit/total/balance transactions থেকে compute হবে
     */
    public function account()
    {
        $accounts = Account::latest()->paginate(30);

        // per-account totals (fast)
        $txAgg = Transactions::selectRaw('account_id, COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(credit),0) as total_credit')
            ->groupBy('account_id')
            ->pluck('total_debit', 'account_id');

        // pluck দুইবার করলে সহজ হয়
        $creditAgg = Transactions::selectRaw('account_id, COALESCE(SUM(credit),0) as total_credit')
            ->groupBy('account_id')
            ->pluck('total_credit', 'account_id');

        // view এ easy use
        return view('account.chart-of-accounts', [
            'accounts' => $accounts,
            'debitAgg' => $txAgg,
            'creditAgg' => $creditAgg,
        ]);
    }
}
