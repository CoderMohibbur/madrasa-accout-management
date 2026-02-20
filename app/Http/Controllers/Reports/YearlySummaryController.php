<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transactions;
use App\Models\TransactionsType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class YearlySummaryController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) ($request->get('year') ?: now()->year);
        if ($year < 1900 || $year > 2200) $year = now()->year;

        $accountId = $request->get('account_id');

        // key->id (no hardcode)
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

        $incomeKeys  = ['student_fee', 'donation', 'income', 'loan_taken'];
        $expenseKeys = ['expense', 'loan_repayment'];

        $incomeTypeIds  = array_values(array_filter(array_map($typeIdByKey, $incomeKeys)));
        $expenseTypeIds = array_values(array_filter(array_map($typeIdByKey, $expenseKeys)));

        $start = Carbon::create($year, 1, 1)->toDateString();
        $end   = Carbon::create($year, 12, 31)->toDateString();

        $base = Transactions::query()
            ->whereDate('transactions_date', '>=', $start)
            ->whereDate('transactions_date', '<=', $end)
            ->when($accountId, fn($q) => $q->where('account_id', $accountId));

        // Income = credit
        $incomeByMonth = (clone $base)
            ->whereIn('transactions_type_id', $incomeTypeIds ?: [-1])
            ->where('credit', '>', 0)
            ->selectRaw('MONTH(transactions_date) as m, SUM(credit) as total')
            ->groupBy('m')
            ->pluck('total', 'm');

        // Expense = debit
        $expenseByMonth = (clone $base)
            ->whereIn('transactions_type_id', $expenseTypeIds ?: [-1])
            ->where('debit', '>', 0)
            ->selectRaw('MONTH(transactions_date) as m, SUM(debit) as total')
            ->groupBy('m')
            ->pluck('total', 'm');

        $monthsBn = [
            1=>'জানুয়ারি',2=>'ফেব্রুয়ারি',3=>'মার্চ',4=>'এপ্রিল',5=>'মে',6=>'জুন',
            7=>'জুলাই',8=>'আগস্ট',9=>'সেপ্টেম্বর',10=>'অক্টোবর',11=>'নভেম্বর',12=>'ডিসেম্বর'
        ];

        $months = collect(range(1, 12))->map(function ($m) use ($year, $monthsBn, $incomeByMonth, $expenseByMonth) {
            $income  = (float) ($incomeByMonth[$m] ?? 0);
            $expense = (float) ($expenseByMonth[$m] ?? 0);

            return [
                'm'        => $m,
                'label'    => $monthsBn[$m] ?? ('Month '.$m),
                'ym'       => sprintf('%04d-%02d', $year, $m),
                'income'   => $income,
                'expense'  => $expense,
                'surplus'  => $income - $expense,
            ];
        });

        $accounts = Account::query()->select('id','name')->orderBy('name')->get();

        $yearIncome  = (float) $months->sum('income');
        $yearExpense = (float) $months->sum('expense');

        return view('reports.yearly-summary', compact(
            'year','accountId','accounts','months','yearIncome','yearExpense'
        ));
    }
}