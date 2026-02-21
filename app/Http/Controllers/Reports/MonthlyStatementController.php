<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transactions;
use App\Models\TransactionsType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MonthlyStatementController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Month input: YYYY-MM
        $month = (string) $request->get('month', now()->format('Y-m'));

        try {
            $c = Carbon::createFromFormat('Y-m', $month);
        } catch (\Throwable $e) {
            $month = now()->format('Y-m');
            $c = Carbon::createFromFormat('Y-m', $month);
        }

        $start = $c->copy()->startOfMonth()->toDateString();
        $end   = $c->copy()->endOfMonth()->toDateString();

        // Optional account filter
        $accountId = $request->get('account_id');

        /**
         * ✅ Type map: key → id (Hardcode forbidden)
         * fallback: if key column missing, match by name
         */
        $typeIdByKey = function (string $key): ?int {
            $typeTable = (new TransactionsType())->getTable();

            if (Schema::hasTable($typeTable) && Schema::hasColumn($typeTable, 'key')) {
                return TransactionsType::query()->where('key', $key)->value('id');
            }

            $name = match ($key) {
                'student_fee'     => 'Student Fee',
                'loan_taken'      => 'Loan Taken',
                'loan_repayment'  => 'Loan Repayment',
                default           => Str::headline($key),
            };

            return TransactionsType::query()->where('name', $name)->value('id');
        };

        // ✅ Phase 7 rules (Income Left, Expense Right)
        $incomeKeys  = ['student_fee', 'donation', 'income', 'loan_taken'];
        $expenseKeys = ['expense', 'loan_repayment'];

        $incomeTypeIds  = array_values(array_filter(array_map($typeIdByKey, $incomeKeys)));
        $expenseTypeIds = array_values(array_filter(array_map($typeIdByKey, $expenseKeys)));

        /**
         * ✅ Category map (expense grouping)
         * catagories table may have only name (no title)
         */
        $catMap = collect();
        if (Schema::hasTable('catagories')) {
            $cols = ['id'];
            if (Schema::hasColumn('catagories', 'name'))  $cols[] = 'name';
            if (Schema::hasColumn('catagories', 'title')) $cols[] = 'title';

            $rows = DB::table('catagories')->select($cols)->get();

            $catMap = $rows->mapWithKeys(function ($r) {
                $label = trim((string)($r->name ?? $r->title ?? ''));
                return [$r->id => $label];
            });
        }

        // ✅ Base query
        $base = Transactions::query()
            ->with(['student', 'donor', 'lender', 'account', 'type'])
            ->whereDate('transactions_date', '>=', $start)
            ->whereDate('transactions_date', '<=', $end)
            ->when($accountId, fn($q) => $q->where('account_id', $accountId));

        /**
         * ✅ Mandatory Accounting Rules (match storeQuick)
         * Income = CREDIT
         * Expense = DEBIT
         */
        $incomeTx = (clone $base)
            ->whereIn('transactions_type_id', $incomeTypeIds ?: [-1])
            ->where('credit', '>', 0)
            ->orderBy('transactions_date')->orderBy('id')
            ->get();

        $expenseTx = (clone $base)
            ->whereIn('transactions_type_id', $expenseTypeIds ?: [-1])
            ->where('debit', '>', 0)
            ->orderBy('transactions_date')->orderBy('id')
            ->get();

        $totalIncome  = (float) $incomeTx->sum(fn($t) => (float)($t->credit ?? 0));
        $totalExpense = (float) $expenseTx->sum(fn($t) => (float)($t->debit ?? 0));
        $surplus      = $totalIncome - $totalExpense;

        // ✅ Party finder (Bangla safe)
        $getParty = function ($tx) {
            $studentName =
                data_get($tx, 'student.full_name') ??
                data_get($tx, 'student.name') ??
                data_get($tx, 'student.student_name');

            $donorName =
                data_get($tx, 'donor.name') ??
                data_get($tx, 'donor.donor_name') ??
                data_get($tx, 'donor.doner_name');

            $lenderName =
                data_get($tx, 'lender.name') ??
                data_get($tx, 'lender.lender_name');

            if ($studentName) return ['label' => 'Student', 'name' => $studentName];
            if ($donorName)   return ['label' => 'Donor',   'name' => $donorName];
            if ($lenderName)  return ['label' => 'Lender',  'name' => $lenderName];

            return ['label' => '-', 'name' => null];
        };

        // ✅ Title getter (title column may not exist → c_s_1 / note safe)
        $getTitle = function ($tx) {
            $t = trim((string)($tx->title ?? ''));
            if ($t !== '') return $t;

            $cs1 = trim((string)($tx->c_s_1 ?? ''));
            if ($cs1 !== '') return $cs1;

            $note = trim((string)($tx->note ?? ''));
            return $note !== '' ? $note : null;
        };

        $getDisplay = function ($tx) use ($getParty, $getTitle) {
            $party = $getParty($tx);
            $title = $getTitle($tx);

            return [
                'party_name'  => $party['name'],
                'party_label' => $party['label'],
                'title'       => $title,
            ];
        };

        // ✅ Expense buckets (General / Construction / Boarding)
        $bucketFromCategory = function ($tx) use ($catMap) {
            $catId = $tx->catagory_id ?? null;
            $name  = $catId ? (string)($catMap[$catId] ?? '') : '';
            $n = Str::lower($name);

            if (Str::contains($n, ['construction', 'নির্মাণ', 'কনস্ট্রাকশন'])) return 'Construction';
            if (Str::contains($n, ['boarding', 'বোর্ডিং', 'hostel', 'হোস্টেল'])) return 'Boarding';
            return 'General';
        };

        $expenseGroups = $expenseTx->groupBy($bucketFromCategory);

        $accounts = Account::query()->select('id', 'name')->orderBy('name')->get();

        $printedAt = now();
        $monthLabel = $c->format('F Y'); // e.g. February 2026

        return view('reports.monthly-statement', compact(
            'month',
            'monthLabel',
            'start',
            'end',
            'accountId',
            'incomeTx',
            'expenseTx',
            'expenseGroups',
            'totalIncome',
            'totalExpense',
            'surplus',
            'accounts',
            'getDisplay',
            'printedAt'
        ));
    }
}
