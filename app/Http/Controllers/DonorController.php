<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AddFessType;
use App\Models\Donor;
use App\Models\Transactions;
use App\Models\TransactionsType;
use App\Support\TransactionLedger;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    public function index()
    {
        $Donors = Donor::all();
        return view('donors.index', compact('Donors'));
    }

    public function create()
    {
        $fees_types = AddFessType::all();
        return view('donors.create', compact('fees_types'));
    }

    public function store(Request $request)
    {
        // ✅ only necessary required (Phase 1 style)
        $validatedData = $request->validate([
            'name'        => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
            'mobile'      => 'nullable|string|max:15',
            'fees_type_id'=> 'nullable|exists:add_fess_types,id', // ✅ optional
            'isActived'   => 'nullable|boolean',
        ]);

        Donor::create([
            'name'        => $validatedData['name'] ?? null,
            'email'       => $validatedData['email'] ?? null,
            'mobile'      => $validatedData['mobile'] ?? null,
            'fees_type_id'=> $validatedData['fees_type_id'] ?? null,
            'isActived'   => $validatedData['isActived'] ?? 1,
            'isDeleted'   => 0,
        ]);

        return redirect()->route('donors.index')->with('success', 'Donor created successfully.');
    }

    public function edit($id)
    {
        $Donor = Donor::findOrFail($id);
        $fees_types = AddFessType::all();
        return view('donors.create', compact('Donor', 'fees_types'));
    }

    public function update(Request $request, Donor $Donor)
    {
        $validated = $request->validate([
            'name'         => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'mobile'       => 'nullable|string|max:15',
            'fees_type_id' => 'nullable|exists:add_fess_types,id',
            'isActived'    => 'nullable|boolean',
        ]);

        $Donor->update([
            'name'         => $validated['name'] ?? null,
            'email'        => $validated['email'] ?? null,
            'mobile'       => $validated['mobile'] ?? null,
            'fees_type_id' => $validated['fees_type_id'] ?? null,
            'isActived'    => $validated['isActived'] ?? 1,
        ]);

        return redirect()->route('donors.index')->with('success', 'Donor updated successfully.');
    }

    public function destroy(Donor $Donor)
    {
        $Donor->delete();
        return redirect()->route('donors.index')->with('success', 'Donor deleted successfully.');
    }

    /**
     * Donation transactions list/add page
     */
    public function donars()
    {
        $transactionss = Transactions::whereNotNull('doner_id')->orderByDesc('id')->get();
        $Donors = Donor::all();
        $accounts = Account::all();

        return view('donors.add-donar', compact('transactionss', 'Donors', 'accounts'));
    }

    /**
     * ✅ Donation create (Phase 1)
     * - no hardcode transactions_type_id
     * - donation is INCOME => credit
     */
    public function donosr_store(Request $request)
    {
        $validated = $request->validate([
            'doner_id'          => 'required|exists:donors,id',
            'total_fees'        => 'required|numeric|min:0.01',   // donation amount
            'c_s_1'             => 'nullable|string|max:250',     // title
            'account_id'        => 'required|exists:accounts,id',
            'note'              => 'nullable|string|max:500',
            'transactions_date' => 'nullable|date',
            'isActived'         => 'required|boolean',
        ]);

        $txDate = $validated['transactions_date'] ?? now()->toDateString();
        $amount = (float) $validated['total_fees'];

        $typeId = TransactionsType::idByKey('donation');
        $split  = TransactionLedger::split('donation', $amount); // ✅ credit

        Transactions::create([
            'doner_id'             => $validated['doner_id'],
            'transactions_type_id' => $typeId,
            'account_id'           => $validated['account_id'],
            'transactions_date'    => $txDate,
            'note'                 => $validated['note'] ?? 'Donation',
            'c_s_1'                => $validated['c_s_1'] ?? 'Donation',
            'total_fees'           => $amount,

            'debit'                => $split['debit'],
            'credit'               => $split['credit'],

            'fess_type_id'         => null,
            'isActived'            => $validated['isActived'] ?? 1,
            'isDeleted'            => 0,
            'created_by_id'        => auth()->id(),
        ]);

        return redirect()->route('add_donar')->with('success', 'Donation added successfully!');
    }

    public function edit_donor($id)
    {
        $transaction = Transactions::findOrFail($id);
        $transactionss = Transactions::whereNotNull('doner_id')->orderByDesc('id')->get();
        $Donors = Donor::all();
        $accounts = Account::all();

        return view('donors.add-donar', compact('transaction', 'Donors', 'accounts', 'transactionss'));
    }

    /**
     * ✅ Donation update (Phase 1)
     * - fix invalid validation
     * - remove hardcode type id
     * - keep donation ledger: credit
     */
    public function update_donor(Request $request, $id)
    {
        $validated = $request->validate([
            'doner_id'          => 'required|exists:donors,id',
            'total_fees'        => 'required|numeric|min:0.01',
            'account_id'        => 'required|exists:accounts,id',
            'c_s_1'             => 'nullable|string|max:250',
            'note'              => 'nullable|string|max:500',
            'transactions_date' => 'nullable|date',
            'isActived'         => 'required|boolean',
        ]);

        $transaction = Transactions::findOrFail($id);

        $txDate = $validated['transactions_date'] ?? now()->toDateString();
        $amount = (float) $validated['total_fees'];

        $typeId = TransactionsType::idByKey('donation');
        $split  = TransactionLedger::split('donation', $amount);

        $transaction->update([
            'doner_id'             => $validated['doner_id'],
            'transactions_type_id' => $typeId,
            'account_id'           => $validated['account_id'],
            'transactions_date'    => $txDate,
            'note'                 => $validated['note'] ?? 'Donation',
            'c_s_1'                => $validated['c_s_1'] ?? 'Donation',
            'total_fees'           => $amount,
            'debit'                => $split['debit'],
            'credit'               => $split['credit'],
            'fess_type_id'         => null,
            'isActived'            => $validated['isActived'],
        ]);

        return redirect()->route('add_donar')->with('success', 'Donation updated successfully.');
    }

    /**
     * ✅ Fix: this should delete donation transaction, not donor
     */
    public function destroy_donor($id)
    {
        $transaction = Transactions::findOrFail($id);

        // Soft delete aware
        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($transaction))) {
            $transaction->delete();
        } else {
            if (\Illuminate\Support\Facades\Schema::hasColumn('transactions', 'isDeleted')) {
                $transaction->isDeleted = true;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('transactions', 'isActived')) {
                $transaction->isActived = false;
            }
            $transaction->save();
        }

        return redirect()->route('add_donar')->with('success', 'Donation deleted successfully.');
    }

    // (Optional) this method seems unrelated, kept as-is
    public function donors_loan(Donor $lender)
    {
        $transactionss = Transactions::with('doner')->whereNotNull('doner_id')->get();
        $Donors = Donor::all();
        $accounts = Account::all();

        return view('lender.add-loan', compact('transactionss', 'Donors', 'accounts'));
    }
}
