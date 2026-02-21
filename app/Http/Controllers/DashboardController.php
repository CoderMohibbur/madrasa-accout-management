<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Student;
use App\Models\Transactions;
use App\Models\TransactionsType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class DashboardController extends Controller
{
    /**
     * ✅ Phase 1 transaction keys
     */
    private const TYPE_KEYS = [
        'student_fee',
        'donation',
        'income',
        'expense',
        'loan_taken',
        'loan_repayment',
    ];

    /**
     * ✅ Safe type id resolver:
     * 1) Prefer key column (TransactionsType::idByKey)
     * 2) Fallback by legacy names (if key column missing / old DB)
     */
    private function typeId(string $key): ?int
    {
        try {
            return TransactionsType::idByKey($key);
        } catch (Throwable $e) {
            // fallback by name
        }

        $map = [
            'student_fee'    => ['Student Fee', 'Student Fees', 'Student Fess', 'Student Fesses'],
            'donation'       => ['Donation', 'Donar', 'Doner', 'Doner Donation'],
            'income'         => ['Income'],
            'expense'        => ['Expense', 'Expens'],
            'loan_taken'     => ['Loan', 'Loan Taken', 'Loan Take'],
            'loan_repayment' => ['Repayment', 'Loan Repayment', 'Loan Pay'],
        ];

        $names = $map[$key] ?? [];

        $q = TransactionsType::query();

        if (!empty($names)) {
            $id = $q->whereIn('name', $names)->value('id');
            return $id ? (int) $id : null;
        }

        // last fallback: like
        $like = str_replace('_', ' ', $key);
        $id = TransactionsType::query()->where('name', 'like', '%' . $like . '%')->value('id');
        return $id ? (int) $id : null;
    }

    /**
     * ✅ Apply common filters to a query
     */
    private function applyCommonTxFilters($q, string $from, string $to, ?int $accountId, ?int $typeId)
    {
        // optional soft delete / flag filters
        if (Schema::hasColumn('transactions', 'deleted_at')) {
            $q->whereNull('deleted_at');
        }
        if (Schema::hasColumn('transactions', 'isDeleted')) {
            $q->where('isDeleted', 0);
        }
        if (Schema::hasColumn('transactions', 'isActived')) {
            $q->where('isActived', 1);
        }

        $q->whereDate('transactions_date', '>=', $from)
            ->whereDate('transactions_date', '<=', $to);

        if ($accountId) {
            $q->where('account_id', $accountId);
        }
        if ($typeId) {
            $q->where('transactions_type_id', $typeId);
        }

        return $q;
    }

    public function index(Request $request)
    {
        // ✅ Date defaults
        $from = $request->input('from') ?: now()->startOfMonth()->toDateString();
        $to   = $request->input('to')   ?: now()->toDateString();

        $accountId = $request->input('account_id') ? (int) $request->input('account_id') : null;
        $typeId    = $request->input('type_id') ? (int) $request->input('type_id') : null;

        // dropdowns
        $accounts = Account::orderBy('name')->get();
        $types    = TransactionsType::orderBy('name')->get();

        // ✅ Type IDs (prefer key system)
        $studentFeeTypeId = $this->typeId('student_fee');
        $donationTypeId   = $this->typeId('donation');
        $incomeTypeId     = $this->typeId('income');
        $expenseTypeId    = $this->typeId('expense');
        $loanTakenTypeId  = $this->typeId('loan_taken');
        $loanRepayTypeId  = $this->typeId('loan_repayment');

        /**
         * =========================
         * A) Base Query (Dashboard filter applied)
         * =========================
         */
        $base = Transactions::query()
            ->with(['student', 'donor', 'lender', 'account', 'type']);

        $this->applyCommonTxFilters($base, $from, $to, $accountId, $typeId);

        $transactionsCount = (clone $base)->count();

        // ✅ Phase 1 ledger totals:
        // income side = credit, expense side = debit
        $totalDebit  = (float) (clone $base)->sum('debit');
        $totalCredit = (float) (clone $base)->sum('credit');
        $net         = $totalCredit - $totalDebit;

        /**
         * =========================
         * B) Student fee paid count + fee collected
         * (Due list needs fee-only base, so we calculate separately without typeId filter)
         * =========================
         */
        $feeBase = Transactions::query();
        $this->applyCommonTxFilters($feeBase, $from, $to, $accountId, null); // ignore type filter

        $feesCollected = 0.0;
        $studentsPaidCount = 0;

        if ($studentFeeTypeId) {
            $feesCollected = (float) (clone $feeBase)
                ->where('transactions_type_id', $studentFeeTypeId)
                ->sum('credit'); // ✅ student_fee is income

            $studentsPaidCount = (int) (clone $feeBase)
                ->where('transactions_type_id', $studentFeeTypeId)
                ->whereNotNull('student_id')
                ->distinct('student_id')
                ->count('student_id');
        }

        $totalStudents = (int) Student::count();
        $studentsUnpaidApprox = max(0, $totalStudents - $studentsPaidCount);

        /**
         * =========================
         * C) Type totals (ledger correct)
         * =========================
         */
        $expenseTotal = $expenseTypeId
            ? (float) (clone $feeBase)->where('transactions_type_id', $expenseTypeId)->sum('debit')
            : 0.0;

        $incomeTotal = $incomeTypeId
            ? (float) (clone $feeBase)->where('transactions_type_id', $incomeTypeId)->sum('credit')
            : 0.0;

        $donationTotal = $donationTypeId
            ? (float) (clone $feeBase)->where('transactions_type_id', $donationTypeId)->sum('credit')
            : 0.0;

        $loanTakenTotal = $loanTakenTypeId
            ? (float) (clone $feeBase)->where('transactions_type_id', $loanTakenTypeId)->sum('credit')
            : 0.0;

        $loanRepaymentTotal = $loanRepayTypeId
            ? (float) (clone $feeBase)->where('transactions_type_id', $loanRepayTypeId)->sum('debit')
            : 0.0;

        $loanOutstanding = $loanTakenTotal - $loanRepaymentTotal;
        $loanPaidPct = $loanTakenTotal > 0
            ? min(100, ($loanRepaymentTotal / $loanTakenTotal) * 100)
            : 0;

        /**
         * =========================
         * D) Recent transactions (filter applied)
         * =========================
         */
        $recent = (clone $base)
            ->orderByDesc('transactions_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        /**
         * =========================
         * E) Account balances (credit - debit)
         * (use feeBase: date+account filters, ignore type filter)
         * =========================
         */
        $accountBalancesQ = DB::table('transactions as tr')
            ->leftJoin('accounts as a', 'a.id', '=', 'tr.account_id')
            ->selectRaw("
                tr.account_id,
                COALESCE(a.name, CONCAT('Account #', tr.account_id)) as account_name,
                SUM(COALESCE(tr.debit,0)) as debit_sum,
                SUM(COALESCE(tr.credit,0)) as credit_sum,
                (SUM(COALESCE(tr.credit,0)) - SUM(COALESCE(tr.debit,0))) as balance
            ")
            ->whereDate('tr.transactions_date', '>=', $from)
            ->whereDate('tr.transactions_date', '<=', $to);

        if (Schema::hasColumn('transactions', 'deleted_at')) $accountBalancesQ->whereNull('tr.deleted_at');
        if (Schema::hasColumn('transactions', 'isDeleted'))  $accountBalancesQ->where('tr.isDeleted', 0);
        if (Schema::hasColumn('transactions', 'isActived'))  $accountBalancesQ->where('tr.isActived', 1);

        if ($accountId) $accountBalancesQ->where('tr.account_id', $accountId);

        $accountBalances = collect(
            $accountBalancesQ
                ->groupBy('tr.account_id', 'a.name')
                ->orderByDesc(DB::raw("(SUM(COALESCE(tr.credit,0)) - SUM(COALESCE(tr.debit,0)))"))
                ->get()
        );

        $maxAbsBalance = (float) $accountBalances->map(fn($r) => abs((float)$r->balance))->max();
        $maxAbsBalance = $maxAbsBalance > 0 ? $maxAbsBalance : 1;

        /**
         * =========================
         * F) Type breakdown (filter applied)
         * =========================
         */
        $typeBreakdownQ = DB::table('transactions as tr')
            ->leftJoin('transactions_types as tt', 'tt.id', '=', 'tr.transactions_type_id')
            ->selectRaw("
                tr.transactions_type_id,
                COALESCE(tt.name, CONCAT('Type #', tr.transactions_type_id)) as type_name,
                COUNT(*) as count,
                SUM(COALESCE(tr.debit,0)) as debit_sum,
                SUM(COALESCE(tr.credit,0)) as credit_sum
            ")
            ->whereDate('tr.transactions_date', '>=', $from)
            ->whereDate('tr.transactions_date', '<=', $to);

        if (Schema::hasColumn('transactions', 'deleted_at')) $typeBreakdownQ->whereNull('tr.deleted_at');
        if (Schema::hasColumn('transactions', 'isDeleted'))  $typeBreakdownQ->where('tr.isDeleted', 0);
        if (Schema::hasColumn('transactions', 'isActived'))  $typeBreakdownQ->where('tr.isActived', 1);

        if ($accountId) $typeBreakdownQ->where('tr.account_id', $accountId);
        if ($typeId)    $typeBreakdownQ->where('tr.transactions_type_id', $typeId);

        $typeBreakdown = $typeBreakdownQ
            ->groupBy('tr.transactions_type_id', 'tt.name')
            ->orderByDesc(DB::raw("SUM(COALESCE(tr.debit,0)) + SUM(COALESCE(tr.credit,0))"))
            ->get();

        /**
         * =========================
         * G) Daily summary (filter applied)
         * net = credit - debit
         * =========================
         */
        $dailyQ = DB::table('transactions as tr')
            ->selectRaw("
                DATE(tr.transactions_date) as d,
                SUM(COALESCE(tr.debit,0)) as debit_sum,
                SUM(COALESCE(tr.credit,0)) as credit_sum,
                (SUM(COALESCE(tr.credit,0)) - SUM(COALESCE(tr.debit,0))) as net
            ")
            ->whereDate('tr.transactions_date', '>=', $from)
            ->whereDate('tr.transactions_date', '<=', $to);

        if (Schema::hasColumn('transactions', 'deleted_at')) $dailyQ->whereNull('tr.deleted_at');
        if (Schema::hasColumn('transactions', 'isDeleted'))  $dailyQ->where('tr.isDeleted', 0);
        if (Schema::hasColumn('transactions', 'isActived'))  $dailyQ->where('tr.isActived', 1);

        if ($accountId) $dailyQ->where('tr.account_id', $accountId);
        if ($typeId)    $dailyQ->where('tr.transactions_type_id', $typeId);

        $daily = $dailyQ
            ->groupBy(DB::raw("DATE(tr.transactions_date)"))
            ->orderBy('d')
            ->get();

        /**
         * =========================
         * H) Due students (fee-only)
         * =========================
         */
        $studentsDueCount = 0;
        $dueStudents = collect();

        if ($studentFeeTypeId) {
            $paidSub = DB::table('transactions')
                ->selectRaw('DISTINCT student_id')
                ->whereNotNull('student_id')
                ->where('transactions_type_id', $studentFeeTypeId)
                ->whereDate('transactions_date', '>=', $from)
                ->whereDate('transactions_date', '<=', $to);

            if (Schema::hasColumn('transactions', 'deleted_at')) $paidSub->whereNull('deleted_at');
            if (Schema::hasColumn('transactions', 'isDeleted'))  $paidSub->where('isDeleted', 0);
            if (Schema::hasColumn('transactions', 'isActived'))  $paidSub->where('isActived', 1);

            if ($accountId) $paidSub->where('account_id', $accountId);

            $studentsPaidCount2 = (int) DB::query()->fromSub($paidSub, 'p')->count();
            $studentsDueCount   = max(0, $totalStudents - $studentsPaidCount2);

            $dueStudents = DB::table('students as s')
                ->leftJoinSub($paidSub, 'p', fn($j) => $j->on('p.student_id', '=', 's.id'))
                ->whereNull('p.student_id')
                ->select('s.id', DB::raw("COALESCE(NULLIF(s.full_name,''), CONCAT('Student #', s.id)) as student_name"))
                ->orderBy('student_name')
                ->limit(8)
                ->get();
        }


        /**
         * =========================
         * H2) Paid students list (fee-only)
         * =========================
         */
        $studentsPaidCountExact = $studentsPaidCount; // fallback
        $paidStudents = collect();

        if ($studentFeeTypeId) {

            // reuse exact paid count from paidSub (more accurate with flags/account filter)
            if (isset($studentsPaidCount2)) {
                $studentsPaidCountExact = (int) $studentsPaidCount2;
            }

            $paidStudentsQ = DB::table('transactions as t')
                ->join('students as s', 's.id', '=', 't.student_id')
                ->whereNotNull('t.student_id')
                ->where('t.transactions_type_id', $studentFeeTypeId)
                ->whereDate('t.transactions_date', '>=', $from)
                ->whereDate('t.transactions_date', '<=', $to);

            // transactions flags
            if (Schema::hasColumn('transactions', 'deleted_at')) $paidStudentsQ->whereNull('t.deleted_at');
            if (Schema::hasColumn('transactions', 'isDeleted'))  $paidStudentsQ->where('t.isDeleted', 0);
            if (Schema::hasColumn('transactions', 'isActived'))  $paidStudentsQ->where('t.isActived', 1);

            // student flags (optional)
            if (Schema::hasColumn('students', 'isDeleted')) $paidStudentsQ->where('s.isDeleted', 0);

            if ($accountId) $paidStudentsQ->where('t.account_id', $accountId);

            $paidStudents = $paidStudentsQ
                ->selectRaw("
            s.id as id,
            COALESCE(NULLIF(s.full_name,''), CONCAT('Student #', s.id)) as student_name,
            SUM(COALESCE(t.credit,0)) as paid_total,
            MAX(t.transactions_date) as last_paid_date
        ")
                ->groupBy('s.id', 'student_name')
                ->orderByDesc('last_paid_date')
                ->limit(8)
                ->get();
        }

        /**
         * =========================
         * I) Expense breakdown by category (expense = debit)
         * =========================
         */
        $expenseByCategory = collect();

        if ($expenseTypeId && Schema::hasTable('catagories')) {
            // Strategy A: transactions.catagory_id
            if (Schema::hasColumn('transactions', 'catagory_id')) {
                $expenseByCategory = DB::table('transactions as t')
                    ->leftJoin('catagories as c', 'c.id', '=', 't.catagory_id')
                    ->where('t.transactions_type_id', $expenseTypeId)
                    ->whereDate('t.transactions_date', '>=', $from)
                    ->whereDate('t.transactions_date', '<=', $to)
                    ->when($accountId, fn($q) => $q->where('t.account_id', $accountId))
                    ->selectRaw('COALESCE(c.name,"Uncategorized") as category_name, SUM(COALESCE(t.debit,0)) as total')
                    ->groupBy('category_name')
                    ->orderByDesc('total')
                    ->limit(8)
                    ->get();
            }
            // Strategy B: transactions.expens_id -> expens.catagory_id
            elseif (Schema::hasColumn('transactions', 'expens_id') && Schema::hasTable('expens')) {
                $expenseByCategory = DB::table('transactions as t')
                    ->leftJoin('expens as e', 'e.id', '=', 't.expens_id')
                    ->leftJoin('catagories as c', 'c.id', '=', 'e.catagory_id')
                    ->where('t.transactions_type_id', $expenseTypeId)
                    ->whereDate('t.transactions_date', '>=', $from)
                    ->whereDate('t.transactions_date', '<=', $to)
                    ->when($accountId, fn($q) => $q->where('t.account_id', $accountId))
                    ->selectRaw('COALESCE(c.name,"Uncategorized") as category_name, SUM(COALESCE(t.debit,0)) as total')
                    ->groupBy('category_name')
                    ->orderByDesc('total')
                    ->limit(8)
                    ->get();
            }
        }

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
            'paidStudents',
            'studentsPaidCountExact',
            'loanPaidPct',
            'expenseByCategory',
            'studentFeeTypeId',
            'loanTakenTypeId',
            'loanRepayTypeId',
            'studentsDueCount',
            'expenseTypeId',
            'incomeTypeId',
            'donationTypeId',
        ));
    }
}
