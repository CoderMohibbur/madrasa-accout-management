<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transactions;
use App\Models\TransactionsType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function transactions(Request $request)
    {
        $from = $request->input('from') ?: Carbon::now()->startOfMonth()->toDateString();
        $to   = $request->input('to')   ?: Carbon::now()->endOfMonth()->toDateString();

        $q = Transactions::query()
            ->with(['student','donor','lender','account','type'])
            ->when($from, fn($qq) => $qq->whereDate('transactions_date','>=',$from))
            ->when($to,   fn($qq) => $qq->whereDate('transactions_date','<=',$to));

        if ($request->filled('account_id')) {
            $q->where('account_id', $request->account_id);
        }
        if ($request->filled('type_id')) {
            $q->where('transactions_type_id', $request->type_id);
        }
        if ($request->filled('search')) {
            $s = trim($request->search);
            $q->where(function($qq) use ($s){
                $qq->where('recipt_no','like',"%{$s}%")
                   ->orWhere('student_book_number','like',"%{$s}%")
                   ->orWhere('note','like',"%{$s}%");
            });
        }

        $transactions = $q->orderByDesc('transactions_date')->orderByDesc('id')
            ->paginate(50)
            ->withQueryString();

        $accounts = Account::orderBy('name')->get();
        $types    = TransactionsType::orderBy('name')->get();

        return view('reports.transactions', compact('transactions','accounts','types','from','to'));
    }

    public function transactionsCsv(Request $request): StreamedResponse
    {
        $from = $request->input('from') ?: Carbon::now()->startOfMonth()->toDateString();
        $to   = $request->input('to')   ?: Carbon::now()->endOfMonth()->toDateString();

        $q = Transactions::query()
            ->with(['student','donor','lender','account','type'])
            ->when($from, fn($qq) => $qq->whereDate('transactions_date','>=',$from))
            ->when($to,   fn($qq) => $qq->whereDate('transactions_date','<=',$to));

        if ($request->filled('account_id')) $q->where('account_id', $request->account_id);
        if ($request->filled('type_id'))    $q->where('transactions_type_id', $request->type_id);
        if ($request->filled('search'))     $q->where('note','like','%'.trim($request->search).'%');

        $filename = "transactions_{$from}_to_{$to}.csv";

        return response()->streamDownload(function() use ($q){
            $out = fopen('php://output', 'w');

            fputcsv($out, ['ID','Date','Type','Party','Account','Debit','Credit','Receipt','Note']);

            $q->orderBy('transactions_date')->orderBy('id')->chunk(500, function($rows) use ($out){
                foreach ($rows as $tx) {
                    $typeName = data_get($tx,'type.name') ?? ('Type #'.$tx->transactions_type_id);

                    $studentName = data_get($tx,'student.name') ?? data_get($tx,'student.student_name');
                    $donorName   = data_get($tx,'donor.name') ?? data_get($tx,'donor.donor_name') ?? data_get($tx,'donor.doner_name');
                    $lenderName  = data_get($tx,'lender.name') ?? data_get($tx,'lender.lender_name');
                    $party = $studentName ?: ($donorName ?: ($lenderName ?: ''));

                    $accountName = data_get($tx,'account.name') ?? '';

                    fputcsv($out, [
                        $tx->id,
                        $tx->transactions_date,
                        $typeName,
                        $party,
                        $accountName,
                        (float)($tx->debit ?? 0),
                        (float)($tx->credit ?? 0),
                        $tx->recipt_no,
                        $tx->note,
                    ]);
                }
            });

            fclose($out);
        }, $filename);
    }
}
