<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Expens;
use App\Models\Transactions;
use App\Models\TransactionsType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ExpenseReportController extends Controller
{
    public function index(Request $request)
    {
        /**
         * view:
         * - monthly (default)
         * - yearly
         */
        $view = (string) $request->get('view', 'monthly');
        if (!in_array($view, ['monthly', 'yearly'], true)) $view = 'monthly';

        // monthly: YYYY-MM
        $month = (string) $request->get('month', now()->format('Y-m'));
        try {
            $mCarbon = Carbon::createFromFormat('Y-m', $month);
        } catch (\Throwable $e) {
            $month = now()->format('Y-m');
            $mCarbon = Carbon::createFromFormat('Y-m', $month);
        }

        // yearly
        $year = (int) ($request->get('year') ?: now()->year);
        if ($year < 1900 || $year > 2200) $year = now()->year;

        // filters
        $accountId = $request->get('account_id');
        $bucket = (string) $request->get('bucket', 'all'); // all|general|boarding|construction
        if (!in_array($bucket, ['all', 'general', 'boarding', 'construction'], true)) $bucket = 'all';

        $catagoryId = $request->get('catagory_id'); // specific category id
        $expenseHeadId = $request->get('expens_id'); // expense head (if transactions has column)

        // ✅ Type map: key → id (Hardcode forbidden)
        $typeIdByKey = function (string $key): ?int {
            $typeTable = (new TransactionsType())->getTable();

            if (Schema::hasTable($typeTable) && Schema::hasColumn($typeTable, 'key')) {
                return TransactionsType::query()->where('key', $key)->value('id');
            }

            $name = match ($key) {
                'loan_taken' => 'Loan Taken',
                'loan_repayment' => 'Loan Repayment',
                'student_fee' => 'Student Fee',
                default => Str::headline($key),
            };

            return TransactionsType::query()->where('name', $name)->value('id');
        };

        // Expense only (debit)
        $expenseTypeId = $typeIdByKey('expense');

        // ✅ Category map
        $catMap = collect();
        if (Schema::hasTable('catagories')) {
            $cols = ['id'];
            if (Schema::hasColumn('catagories', 'name'))  $cols[] = 'name';
            if (Schema::hasColumn('catagories', 'title')) $cols[] = 'title';

            $rows = DB::table('catagories')->select($cols)->orderBy('name')->get();

            $catMap = $rows->mapWithKeys(function ($r) {
                $label = trim((string)($r->name ?? $r->title ?? ''));
                return [$r->id => $label];
            });
        }

        // ✅ bucket resolver from category name
        $bucketFromCategoryId = function (?int $cid) use ($catMap) {
            $name = $cid ? (string)($catMap[$cid] ?? '') : '';
            $n = Str::lower($name);

            if (Str::contains($n, ['construction', 'নির্মাণ', 'কনস্ট্রাকশন'])) return 'construction';
            if (Str::contains($n, ['boarding', 'বোর্ডিং', 'hostel', 'হোস্টেল'])) return 'boarding';
            return 'general';
        };

        // ✅ transactions expense head column detect (optional)
        $txTable = (new Transactions())->getTable();
        $headCol = null;
        foreach (['expens_id', 'expense_id', 'expen_id'] as $col) {
            if (Schema::hasTable($txTable) && Schema::hasColumn($txTable, $col)) {
                $headCol = $col;
                break;
            }
        }

        // ✅ base query (only expense debit)
        $base = Transactions::query()
            ->with(['account', 'type'])
            ->when($expenseTypeId, fn($q) => $q->where('transactions_type_id', $expenseTypeId))
            ->where('debit', '>', 0)
            ->when($accountId, fn($q) => $q->where('account_id', $accountId))
            ->when($catagoryId, fn($q) => $q->where('catagory_id', $catagoryId))
            ->when($headCol && $expenseHeadId, fn($q) => $q->where($headCol, $expenseHeadId));

        // ✅ bucket filter (general/boarding/construction)
        if ($bucket !== 'all') {
            // filter by category name map -> collect matching category ids
            $matchIds = $catMap->filter(function ($label, $id) use ($bucket) {
                $n = Str::lower((string)$label);
                return match ($bucket) {
                    'construction' => Str::contains($n, ['construction', 'নির্মাণ', 'কনস্ট্রাকশন']),
                    'boarding' => Str::contains($n, ['boarding', 'বোর্ডিং', 'hostel', 'হোস্টেল']),
                    'general' => !(Str::contains($n, ['construction', 'নির্মাণ', 'কনস্ট্রাকশন', 'boarding', 'বোর্ডিং', 'hostel', 'হোস্টেল'])),
                    default => true,
                };
            })->keys()->all();

            // যদি category table empty হয়, general fallback (no filter)
            if (!empty($matchIds)) {
                $base->whereIn('catagory_id', $matchIds);
            } elseif ($bucket !== 'all') {
                // empty -> show none
                $base->whereRaw('1=0');
            }
        }

        // ✅ date range based on view
        if ($view === 'yearly') {
            $start = Carbon::create($year, 1, 1)->startOfDay()->toDateString();
            $end = Carbon::create($year, 12, 31)->endOfDay()->toDateString();
        } else {
            $start = $mCarbon->copy()->startOfMonth()->toDateString();
            $end = $mCarbon->copy()->endOfMonth()->toDateString();
        }

        $base->whereDate('transactions_date', '>=', $start)
            ->whereDate('transactions_date', '<=', $end);

        // ✅ monthly list (always useful, even in yearly when clicking details)
        $rows = (clone $base)
            ->orderBy('transactions_date')->orderBy('id')
            ->get();

        $periodTotal = (float) $rows->sum(fn($t) => (float)($t->debit ?? 0));

        // ✅ yearly month-wise summary (only for yearly view)
        $months = collect();
        $yearTotal = 0.0;

        if ($view === 'yearly') {
            $byMonth = (clone $base)
                ->selectRaw('MONTH(transactions_date) as m, SUM(debit) as total')
                ->groupBy('m')
                ->pluck('total', 'm');

            $monthsEn = [
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
            ];

            $months = collect(range(1, 12))->map(function ($m) use ($monthsEn, $byMonth, $year) {
                return [
                    'm'     => $m,
                    'label' => $monthsEn[$m] ?? ('Month ' . $m),
                    'ym'    => sprintf('%04d-%02d', $year, $m),
                    'total' => (float)($byMonth[$m] ?? 0),
                ];
            });

            $yearTotal = (float) $months->sum('total');
        }

        // ✅ lists for filters
        $accounts = Account::query()->select('id', 'name')->orderBy('name')->get();
        $categories = $catMap->map(fn($label, $id) => ['id' => $id, 'label' => $label])->values();

        // expense heads (from settings)
        $expenseHeads = Expens::query()->notDeleted()->orderBy('name')->get(['id', 'name']);

        $printedAt = now();
        $monthLabel = $mCarbon->format('F Y'); // for header

        return view('reports.expense-report', compact(
            'view',
            'month',
            'monthLabel',
            'year',
            'start',
            'end',
            'accountId',
            'bucket',
            'catagoryId',
            'expenseHeadId',
            'headCol',
            'accounts',
            'categories',
            'expenseHeads',
            'rows',
            'periodTotal',
            'months',
            'yearTotal',
            'printedAt'
        ));
    }
}
