<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Account;
use App\Models\Student;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\TransactionsType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    private function typeIdByKey(string $key): ?int
    {
        try {
            return TransactionsType::query()->where('key', $key)->value('id');
        } catch (Throwable $e) {
            return null; // যদি key কলাম না থাকে, fallback হবে
        }
    }

    public function index(Request $request)
    {
        $from = $request->input('from') ?: now()->startOfMonth()->toDateString();
        $to   = $request->input('to')   ?: now()->toDateString();

        $accountId = $request->input('account_id');
        $typeId    = $request->input('type_id');

        // Filter dropdown data
        $accounts = Account::orderBy('name')->get();
        $types    = TransactionsType::orderBy('name')->get();

        // Type IDs (key system)
        $studentFeeTypeId   = $this->typeIdByKey('student_fee');
        $donationTypeId     = $this->typeIdByKey('donation');
        $expenseTypeId      = $this->typeIdByKey('expense');
        $incomeTypeId       = $this->typeIdByKey('income');
        $loanTakenTypeId    = $this->typeIdByKey('loan_taken');
        $loanRepayTypeId    = $this->typeIdByKey('loan_repayment');

        // Base transaction query (filters)
        $base = Transactions::query()
            ->with(['student', 'donor', 'lender', 'account', 'type']);

        if ($from) $base->whereDate('transactions_date', '>=', $from);
        if ($to)   $base->whereDate('transactions_date', '<=', $to);
        if ($accountId) $base->where('account_id', $accountId);
        if ($typeId)    $base->where('transactions_type_id', $typeId);

        $transactionsCount = (clone $base)->count();
        $totalDebit  = (float) (clone $base)->sum('debit');
        $totalCredit = (float) (clone $base)->sum('credit');
        $net = $totalDebit - $totalCredit;

        // Fees collected (student fee debit)
        $feesCollected = 0.0;
        $studentsPaidCount = 0;

        if ($studentFeeTypeId) {
            $feesCollected = (float) (clone $base)
                ->where('transactions_type_id', $studentFeeTypeId)
                ->sum('debit');

            $studentsPaidCount = (int) (clone $base)
                ->where('transactions_type_id', $studentFeeTypeId)
                ->whereNotNull('student_id')
                ->distinct('student_id')
                ->count('student_id');
        }

        // Expense/Income totals
        $expenseTotal = $expenseTypeId
            ? (float) (clone $base)->where('transactions_type_id', $expenseTypeId)->sum('credit')
            : 0.0;

        $incomeTotal = $incomeTypeId
            ? (float) (clone $base)->where('transactions_type_id', $incomeTypeId)->sum('debit')
            : 0.0;

        $donationTotal = $donationTypeId
            ? (float) (clone $base)->where('transactions_type_id', $donationTypeId)->sum('debit')
            : 0.0;

        // Loan summary
        $loanTakenTotal = $loanTakenTypeId
            ? (float) (clone $base)->where('transactions_type_id', $loanTakenTypeId)->sum('debit')
            : 0.0;

        $loanRepaymentTotal = $loanRepayTypeId
            ? (float) (clone $base)->where('transactions_type_id', $loanRepayTypeId)->sum('credit')
            : 0.0;

        $loanOutstanding = $loanTakenTotal - $loanRepaymentTotal;

        // Total students (all)
        $totalStudents = (int) Student::count();
        $studentsUnpaidApprox = max(0, $totalStudents - $studentsPaidCount); // range-based approx

        // Recent transactions
        $recent = (clone $base)
            ->orderByDesc('transactions_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // Account balances (Bank wise)
        $accountBalancesQ = DB::table('transactions as tr')
            ->leftJoin('accounts as a', 'a.id', '=', 'tr.account_id')
            ->selectRaw("
                tr.account_id,
                COALESCE(a.name, CONCAT('Account #', tr.account_id)) as account_name,
                SUM(COALESCE(tr.debit,0)) as debit_sum,
                SUM(COALESCE(tr.credit,0)) as credit_sum,
                (SUM(COALESCE(tr.debit,0)) - SUM(COALESCE(tr.credit,0))) as balance
            ")
            ->when($from, fn($q) => $q->whereDate('tr.transactions_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('tr.transactions_date', '<=', $to))
            ->when($typeId, fn($q) => $q->where('tr.transactions_type_id', $typeId))
            ->when($accountId, fn($q) => $q->where('tr.account_id', $accountId))
            ->groupBy('tr.account_id', 'a.name')
            ->orderByDesc(DB::raw("(SUM(COALESCE(tr.debit,0)) - SUM(COALESCE(tr.credit,0)))"));

        $accountBalances = collect($accountBalancesQ->get());
        $maxAbsBalance = (float) $accountBalances->map(fn($r) => abs((float)$r->balance))->max();
        $maxAbsBalance = $maxAbsBalance > 0 ? $maxAbsBalance : 1;

        // Type breakdown
        $typeBreakdown = DB::table('transactions as tr')
            ->leftJoin('transactions_types as tt', 'tt.id', '=', 'tr.transactions_type_id')
            ->selectRaw("
                tr.transactions_type_id,
                COALESCE(tt.name, CONCAT('Type #', tr.transactions_type_id)) as type_name,
                COUNT(*) as count,
                SUM(COALESCE(tr.debit,0)) as debit_sum,
                SUM(COALESCE(tr.credit,0)) as credit_sum
            ")
            ->when($from, fn($q) => $q->whereDate('tr.transactions_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('tr.transactions_date', '<=', $to))
            ->when($accountId, fn($q) => $q->where('tr.account_id', $accountId))
            ->when($typeId, fn($q) => $q->where('tr.transactions_type_id', $typeId))
            ->groupBy('tr.transactions_type_id', 'tt.name')
            ->orderByDesc(DB::raw("SUM(COALESCE(tr.debit,0)) + SUM(COALESCE(tr.credit,0))"))
            ->get();

        // Daily summary
        $daily = DB::table('transactions as tr')
            ->selectRaw("
                DATE(tr.transactions_date) as d,
                SUM(COALESCE(tr.debit,0)) as debit_sum,
                SUM(COALESCE(tr.credit,0)) as credit_sum,
                (SUM(COALESCE(tr.debit,0)) - SUM(COALESCE(tr.credit,0))) as net
            ")
            ->when($from, fn($q) => $q->whereDate('tr.transactions_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('tr.transactions_date', '<=', $to))
            ->when($accountId, fn($q) => $q->where('tr.account_id', $accountId))
            ->when($typeId, fn($q) => $q->where('tr.transactions_type_id', $typeId))
            ->groupBy(DB::raw("DATE(tr.transactions_date)"))
            ->orderBy('d')
            ->get();

        // ✅ আপনার existing from/to logic থাকলে সেটা রাখুন
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        // ✅ আপনার existing filters
        $accountId = $request->get('account_id');
        $typeId    = $request->get('type_id');

        // =========================
        // 1) Student Fees "বাকি" (এই রেঞ্জে কারা pay করেনি)
        // =========================
        $studentFeeTypeId = DB::table('transactions_types')
            ->whereIn('name', ['Student Fee', 'Student Fees'])
            ->value('id');

        // fallback (যদি নাম একটু আলাদা থাকে)
        if (!$studentFeeTypeId) {
            $studentFeeTypeId = DB::table('transactions_types')
                ->where('name', 'like', '%student%')
                ->where('name', 'like', '%fee%')
                ->value('id');
        }

        $totalStudents = DB::table('students')->count();

        $paidSub = DB::table('transactions')
            ->selectRaw('DISTINCT student_id')
            ->whereNotNull('student_id')
            ->when($studentFeeTypeId, fn($q) => $q->where('transactions_type_id', $studentFeeTypeId))
            ->whereBetween('transactions_date', [$from, $to]);

        // account/type filter apply (যদি আপনি fee-baki কেও filter অনুযায়ী দেখতে চান)
        if ($accountId) $paidSub->where('account_id', $accountId);

        $studentsPaidCount = DB::query()->fromSub($paidSub, 'p')->count();

        $studentsDueCount = max(0, $totalStudents - $studentsPaidCount);

        $studentNameExpr = "
    CASE
        WHEN NULLIF(s.full_name,'') IS NOT NULL THEN s.full_name
        ELSE TRIM(CONCAT(COALESCE(s.first_name,''),' ',COALESCE(s.last_name,'')))
    END
";

        $dueStudents = DB::table('students as s')
            ->leftJoinSub($paidSub, 'p', fn($j) => $j->on('p.student_id', '=', 's.id'))
            ->whereNull('p.student_id')
            ->select('s.id', DB::raw("$studentNameExpr as student_name"))
            ->orderByRaw("$studentNameExpr asc")
            ->limit(8)
            ->get();


        // =========================
        // 2) Loan status (taken / repaid / outstanding)
        // =========================
        $loanTakenTypeId = DB::table('transactions_types')
            ->whereIn('name', ['Loan', 'Loan Taken', 'Loan Take'])
            ->value('id');

        if (!$loanTakenTypeId) {
            $loanTakenTypeId = DB::table('transactions_types')->where('name', 'like', '%loan%')->value('id');
        }

        $loanRepayTypeId = DB::table('transactions_types')
            ->whereIn('name', ['Repayment', 'Loan Repayment', 'Loan Pay'])
            ->value('id');

        if (!$loanRepayTypeId) {
            $loanRepayTypeId = DB::table('transactions_types')->where('name', 'like', '%repay%')->value('id');
        }

        $loanTakenTotal = DB::table('transactions')
            ->whereBetween('transactions_date', [$from, $to])
            ->when($loanTakenTypeId, fn($q) => $q->where('transactions_type_id', $loanTakenTypeId))
            ->when($accountId, fn($q) => $q->where('account_id', $accountId))
            ->sum(DB::raw('COALESCE(credit,0) + COALESCE(debit,0)')); // আপনার loan কোন কলামে যায় সেটার ওপর ভিত্তি করে

        $loanRepaymentTotal = DB::table('transactions')
            ->whereBetween('transactions_date', [$from, $to])
            ->when($loanRepayTypeId, fn($q) => $q->where('transactions_type_id', $loanRepayTypeId))
            ->when($accountId, fn($q) => $q->where('account_id', $accountId))
            ->sum(DB::raw('COALESCE(credit,0) + COALESCE(debit,0)'));

        $loanOutstanding = max(0, (float)$loanTakenTotal - (float)$loanRepaymentTotal);

        $loanPaidPct = $loanTakenTotal > 0
            ? min(100, ((float)$loanRepaymentTotal / (float)$loanTakenTotal) * 100)
            : 0;

        // =========================
        // 3) Expense breakdown (কিসের জন্য খরচ)
        // =========================
        $expenseTypeId = DB::table('transactions_types')
            ->whereIn('name', ['Expense', 'Expens'])
            ->value('id');

        if (!$expenseTypeId) {
            $expenseTypeId = DB::table('transactions_types')->where('name', 'like', '%expens%')->orWhere('name', 'like', '%expense%')->value('id');
        }

        $expenseByCategory = collect();

        // Strategy A: transactions এ catagory_id থাকলে
        if (Schema::hasColumn('transactions', 'catagory_id') && Schema::hasTable('catagories')) {
            $expenseByCategory = DB::table('transactions as t')
                ->leftJoin('catagories as c', 'c.id', '=', 't.catagory_id')
                ->whereBetween('t.transactions_date', [$from, $to])
                ->when($expenseTypeId, fn($q) => $q->where('t.transactions_type_id', $expenseTypeId))
                ->when($accountId, fn($q) => $q->where('t.account_id', $accountId))
                ->selectRaw('COALESCE(c.name,"Uncategorized") as category_name, SUM(COALESCE(t.credit,0)) as total')
                ->groupBy('category_name')
                ->orderByDesc('total')
                ->limit(8)
                ->get();
        }
        // Strategy B: transactions এ expens_id থাকলে → expens টেবিল থেকে catagory
        elseif (Schema::hasColumn('transactions', 'expens_id') && Schema::hasTable('expens') && Schema::hasTable('catagories')) {
            $expenseByCategory = DB::table('transactions as t')
                ->leftJoin('expens as e', 'e.id', '=', 't.expens_id')
                ->leftJoin('catagories as c', 'c.id', '=', 'e.catagory_id')
                ->whereBetween('t.transactions_date', [$from, $to])
                ->when($expenseTypeId, fn($q) => $q->where('t.transactions_type_id', $expenseTypeId))
                ->when($accountId, fn($q) => $q->where('t.account_id', $accountId))
                ->selectRaw('COALESCE(c.name,"Uncategorized") as category_name, SUM(COALESCE(t.credit,0)) as total')
                ->groupBy('category_name')
                ->orderByDesc('total')
                ->limit(8)
                ->get();
        }

        // ✅ আপনার existing $expenseTotal আছে—না থাকলে এখানে sum করে নিন
        $expenseTotal = $expenseTotal ?? (float) DB::table('transactions')
            ->whereBetween('transactions_date', [$from, $to])
            ->when($expenseTypeId, fn($q) => $q->where('transactions_type_id', $expenseTypeId))
            ->when($accountId, fn($q) => $q->where('account_id', $accountId))
            ->sum(DB::raw('COALESCE(credit,0)'));

        return view('dashboard.index', compact(
            'from',
            'to',
            'accountId',
            'typeId',
            'accounts',
            'types',
            'transactionsCount',
            'totalStudents',
            'studentsPaidCount',
            'studentsUnpaidApprox',
            'feesCollected',
            'expenseTotal',
            'incomeTotal',
            'donationTotal',
            'loanTakenTotal',
            'loanRepaymentTotal',
            'loanOutstanding',
            'totalDebit',
            'totalCredit',
            'net',
            'recent',
            'typeBreakdown',
            'daily',
            'accountBalances',
            'maxAbsBalance',
            'dueStudents',
            'loanPaidPct',
            'expenseByCategory',
            'studentFeeTypeId',
            'loanTakenTypeId',
            'loanRepayTypeId',
            'studentsDueCount',
            'dueStudents',

        ));
    }
}
