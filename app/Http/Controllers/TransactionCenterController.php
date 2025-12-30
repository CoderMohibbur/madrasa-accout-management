<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\Lender;
use App\Models\Account;
use App\Models\Student;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\TransactionsType;
use Illuminate\Validation\ValidationException;

class TransactionCenterController extends Controller
{
    public function index(Request $request)
    {
        $q = Transactions::query()
            ->with(['student', 'donor', 'lender', 'account', 'type']);

        // Date range
        if ($request->filled('from')) {
            $q->whereDate('transactions_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $q->whereDate('transactions_date', '<=', $request->to);
        }

        // Account
        if ($request->filled('account_id')) {
            $q->where('account_id', $request->account_id);
        }

        // Type
        if ($request->filled('type_id')) {
            $q->where('transactions_type_id', $request->type_id);
        }

        // Search (safe: unknown column avoid)
        if ($request->filled('search')) {
            $s = trim($request->search);

            $q->where(function ($qq) use ($s) {
                $qq->where('recipt_no', 'like', "%{$s}%")
                    ->orWhere('student_book_number', 'like', "%{$s}%")
                    ->orWhere('note', 'like', "%{$s}%");

                // numeric হলে ids দিয়েও match
                if (ctype_digit($s)) {
                    $qq->orWhere('id', (int)$s)
                        ->orWhere('student_id', (int)$s)
                        ->orWhere('doner_id', (int)$s)
                        ->orWhere('lender_id', (int)$s);
                }
            });
        }

        $transactions = $q->orderByDesc('transactions_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $accounts = Account::orderBy('name')->get();
        $types    = TransactionsType::orderBy('name')->get();

        $students = Student::orderBy('id', 'desc')->get();
        $donors   = Donor::orderBy('id', 'desc')->get();
        $lenders  = Lender::orderBy('id', 'desc')->get();

        return view('transactions.center', compact('transactions', 'accounts', 'types', 'students', 'donors', 'lenders'));
    }

    // =========================
    // Phase 2: Quick Store
    // =========================

    private function ensureSingleParty(array $data): void
    {
        $count = 0;
        foreach (['student_id', 'doner_id', 'lender_id'] as $k) {
            if (!empty($data[$k])) $count++;
        }
        if ($count > 1) {
            throw ValidationException::withMessages([
                'party' => 'এক transaction এ শুধু ১টা party হবে: student_id OR doner_id OR lender_id (একসাথে একাধিক নয়)।',
            ]);
        }
    }

    private function calcTotalFees(array $data): float
    {
        $monthly    = (float)($data['monthly_fees'] ?? 0);
        $boarding   = (float)($data['boarding_fees'] ?? 0);
        $management = (float)($data['management_fees'] ?? 0);
        $exam       = (float)($data['exam_fees'] ?? 0);
        $others     = (float)($data['others_fees'] ?? 0);
        return $monthly + $boarding + $management + $exam + $others;
    }

    public function storeQuick(Request $request)
    {
        // ✅ 1) type_key required (student_fee/donation/loan_taken/loan_repayment/expense/income)
        $typeKey = $request->input('type_key');
        if (!$typeKey) {
            return back()->with('error', 'type_key missing.');
        }

        // ✅ 2) transactions_types থেকে key দিয়ে id বের করি
        $type = TransactionsType::where('key', $typeKey)->first();
        if (!$type) {
            return back()->with('error', "Transaction type key not found: {$typeKey}. আগে transactions_types টেবিলে key সেট করুন।");
        }

        // ✅ 3) base validation
        $baseRules = [
            'type_key'          => 'required|string',
            'account_id'        => 'required|exists:accounts,id',
            'transactions_date' => 'nullable|date',
            'note'              => 'nullable|string|max:500',
            'recipt_no'         => 'nullable|string|max:255',
        ];

        // ✅ 4) type-specific validation + ledger rules
        $typeRules = [];
        $txData = [
            'transactions_type_id' => $type->id,
            'account_id'           => null, // validated থেকে সেট হবে
            'transactions_date'    => null,
            'created_by_id'        => auth()->id(),
            'isActived'            => true,
            'isDeleted'            => false,
        ];

        if ($typeKey === 'student_fee') {
            $typeRules = [
                'student_id'        => 'required|exists:students,id',
                'monthly_fees'      => 'nullable|numeric|min:0',
                'boarding_fees'     => 'nullable|numeric|min:0',
                'management_fees'   => 'nullable|numeric|min:0',
                'exam_fees'         => 'nullable|numeric|min:0',
                'others_fees'       => 'nullable|numeric|min:0',
                'total_fees'        => 'nullable|numeric|min:0',
                'student_book_number' => 'nullable|string|max:255',
            ];
        }

        if ($typeKey === 'donation') {
            $typeRules = [
                'doner_id' => 'required|exists:donors,id',
                'amount'   => 'required|numeric|min:0.01',
            ];
        }

        if ($typeKey === 'loan_taken') {
            $typeRules = [
                'lender_id' => 'required|exists:lenders,id',
                'amount'    => 'required|numeric|min:0.01',
            ];
        }

        if ($typeKey === 'loan_repayment') {
            $typeRules = [
                'lender_id' => 'required|exists:lenders,id',
                'amount'    => 'required|numeric|min:0.01',
            ];
        }

        if ($typeKey === 'expense') {
            $typeRules = [
                'title'  => 'required|string|max:255',
                'amount' => 'required|numeric|min:0.01',
            ];
        }

        if ($typeKey === 'income') {
            $typeRules = [
                'title'  => 'required|string|max:255',
                'amount' => 'required|numeric|min:0.01',
            ];
        }

        // Unknown key guard
        if (empty($typeRules) && !in_array($typeKey, ['student_fee', 'donation', 'loan_taken', 'loan_repayment', 'expense', 'income'])) {
            return back()->with('error', "Unknown type_key: {$typeKey}");
        }

        $validated = $request->validate(array_merge($baseRules, $typeRules));

        // ✅ party integrity
        $this->ensureSingleParty($validated);

        // ✅ default date
        $txDate = $validated['transactions_date'] ?? Carbon::today()->toDateString();

        // ✅ base assign
        $txData['account_id']        = $validated['account_id'];
        $txData['transactions_date'] = $txDate;
        $txData['note']              = $validated['note'] ?? null;
        $txData['recipt_no']          = $validated['recipt_no'] ?? null;

        // ✅ fill party ids (only one expected)
        $txData['student_id'] = $validated['student_id'] ?? null;
        $txData['doner_id']   = $validated['doner_id'] ?? null;
        $txData['lender_id']  = $validated['lender_id'] ?? null;

        // ✅ Type-wise save logic
        if ($typeKey === 'student_fee') {
            $txData['student_book_number'] = $validated['student_book_number'] ?? null;

            $txData['monthly_fees']     = $validated['monthly_fees'] ?? null;
            $txData['boarding_fees']    = $validated['boarding_fees'] ?? null;
            $txData['management_fees']  = $validated['management_fees'] ?? null;
            $txData['exam_fees']        = $validated['exam_fees'] ?? null;
            $txData['others_fees']      = $validated['others_fees'] ?? null;

            $total = isset($validated['total_fees'])
                ? (float)$validated['total_fees']
                : $this->calcTotalFees($validated);

            $txData['total_fees'] = $total;

            // Ledger: student_fee = cash in
            $txData['debit']  = $total;
            $txData['credit'] = 0;
        }

        if ($typeKey === 'donation') {
            $amount = (float)$validated['amount'];
            // Ledger: donation = cash in
            $txData['debit']  = $amount;
            $txData['credit'] = 0;
            $txData['total_fees'] = null;
        }

        if ($typeKey === 'loan_taken') {
            $amount = (float)$validated['amount'];
            // Ledger: loan taken = cash in
            $txData['debit']  = $amount;
            $txData['credit'] = 0;
            $txData['total_fees'] = null;
        }

        if ($typeKey === 'loan_repayment') {
            $amount = (float)$validated['amount'];
            // Ledger: repayment = cash out
            $txData['debit']  = 0;
            $txData['credit'] = $amount;
            $txData['total_fees'] = null;
        }

        if ($typeKey === 'expense') {
            $amount = (float)$validated['amount'];
            // title কে note এ append করে রাখি (আপনি চাইলে আলাদা কলাম পরে)
            $title = $validated['title'];
            $txData['note'] = trim(($title ? "[Expense] {$title} " : '') . ($txData['note'] ?? ''));

            // Ledger: expense = cash out
            $txData['debit']  = 0;
            $txData['credit'] = $amount;
            $txData['total_fees'] = null;

            // No party for expense
            $txData['student_id'] = null;
            $txData['doner_id']   = null;
            $txData['lender_id']  = null;
        }

        if ($typeKey === 'income') {
            $amount = (float)$validated['amount'];
            $title = $validated['title'];
            $txData['note'] = trim(($title ? "[Income] {$title} " : '') . ($txData['note'] ?? ''));

            // Ledger: income = cash in
            $txData['debit']  = $amount;
            $txData['credit'] = 0;
            $txData['total_fees'] = null;

            // No party for income (optional)
            $txData['student_id'] = null;
            $txData['doner_id']   = null;
            $txData['lender_id']  = null;
        }

        Transactions::create($txData);

        return redirect()->to('/transaction-center')->with('success', 'Transaction saved successfully!');
    }
}
