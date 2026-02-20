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
    /**
     * ✅ Range resolve
     * Priority: month (YYYY-MM) > year (YYYY) > from/to (default current month)
     */
    private function resolveRange(Request $request): array
    {
        // Month
        if ($request->filled('month')) {
            $m = (string) $request->month;
            if (preg_match('/^\d{4}-\d{2}$/', $m)) {
                try {
                    $c = Carbon::createFromFormat('Y-m', $m);
                    return [$c->copy()->startOfMonth()->toDateString(), $c->copy()->endOfMonth()->toDateString()];
                } catch (\Throwable $e) {
                    // fallback below
                }
            }
        }

        // Year
        if ($request->filled('year')) {
            $y = (int) $request->year;
            if ($y >= 1900 && $y <= 2200) {
                return [
                    Carbon::create($y, 1, 1)->toDateString(),
                    Carbon::create($y, 12, 31)->toDateString(),
                ];
            }
        }

        // from/to fallback
        $from = $request->input('from');
        $to   = $request->input('to');

        try {
            $from = $from ? Carbon::parse($from)->toDateString() : Carbon::now()->startOfMonth()->toDateString();
        } catch (\Throwable $e) {
            $from = Carbon::now()->startOfMonth()->toDateString();
        }

        try {
            $to = $to ? Carbon::parse($to)->toDateString() : Carbon::now()->endOfMonth()->toDateString();
        } catch (\Throwable $e) {
            $to = Carbon::now()->endOfMonth()->toDateString();
        }

        return [$from, $to];
    }

    /**
     * ✅ Shared query builder (List + CSV + Print/PDF সব জায়গায় same filter)
     * Bangla keyword supported (utf8mb4 থাকলেই OK)
     */
    private function buildTransactionsQuery(Request $request, string $from, string $to)
    {
        $q = Transactions::query()
            ->with(['student', 'donor', 'lender', 'account', 'type'])
            ->when($from, fn($qq) => $qq->whereDate('transactions_date', '>=', $from))
            ->when($to,   fn($qq) => $qq->whereDate('transactions_date', '<=', $to));

        if ($request->filled('account_id')) {
            $q->where('account_id', $request->account_id);
        }

        if ($request->filled('type_id')) {
            $q->where('transactions_type_id', $request->type_id);
        }

        if ($request->filled('search')) {
            $s = trim((string) $request->search);

            $q->where(function ($qq) use ($s) {
                // Local columns
                $qq->where('recipt_no', 'like', "%{$s}%")
                    ->orWhere('student_book_number', 'like', "%{$s}%")
                    ->orWhere('note', 'like', "%{$s}%")
                    ->orWhere('c_s_1', 'like', "%{$s}%");

                // Relations (Bangla safe)
                $qq->orWhereHas('student', function ($q2) use ($s) {
                    $q2->where('full_name', 'like', "%{$s}%")
                        ->orWhere('name', 'like', "%{$s}%")
                        ->orWhere('student_name', 'like', "%{$s}%");
                });

                $qq->orWhereHas('donor', function ($q2) use ($s) {
                    $q2->where('name', 'like', "%{$s}%")
                        ->orWhere('donor_name', 'like', "%{$s}%")
                        ->orWhere('doner_name', 'like', "%{$s}%");
                });

                $qq->orWhereHas('lender', function ($q2) use ($s) {
                    $q2->where('name', 'like', "%{$s}%")
                        ->orWhere('lender_name', 'like', "%{$s}%");
                });
            });
        }

        return $q;
    }

    /**
     * ✅ Uniform party name resolver (Bangla friendly)
     */
    private function partyName($tx): string
    {
        $studentName =
            data_get($tx, 'student.full_name')
            ?? data_get($tx, 'student.name')
            ?? data_get($tx, 'student.student_name');

        $donorName =
            data_get($tx, 'donor.name')
            ?? data_get($tx, 'donor.donor_name')
            ?? data_get($tx, 'donor.doner_name');

        $lenderName =
            data_get($tx, 'lender.name')
            ?? data_get($tx, 'lender.lender_name');

        return (string) ($studentName ?: ($donorName ?: ($lenderName ?: '')));
    }

    // =========================
    // ✅ Report Hub: Transactions List
    // =========================
    public function transactions(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);
        $q = $this->buildTransactionsQuery($request, $from, $to);

        // ✅ Totals (same filter)
        $totalDebit  = (float) (clone $q)->sum('debit');
        $totalCredit = (float) (clone $q)->sum('credit');

        $transactions = $q->orderByDesc('transactions_date')
            ->orderByDesc('id')
            ->paginate(50)
            ->withQueryString();

        $accounts = Account::orderBy('name')->get();
        $types    = TransactionsType::orderBy('name')->get();

        // keep for UI
        $month = $request->input('month');
        $year  = $request->input('year');

        return view('reports.transactions', compact(
            'transactions',
            'accounts',
            'types',
            'from',
            'to',
            'month',
            'year',
            'totalDebit',
            'totalCredit'
        ));
    }

    // =========================
    // ✅ CSV Export (Bangla Excel Support via UTF-8 BOM)
    // =========================
    public function transactionsCsv(Request $request): StreamedResponse
    {
        [$from, $to] = $this->resolveRange($request);
        $q = $this->buildTransactionsQuery($request, $from, $to);

        $filename = "transactions_{$from}_to_{$to}.csv";

        return response()->streamDownload(function () use ($q) {
            $out = fopen('php://output', 'w');

            // ✅ UTF-8 BOM — Excel এ Bangla ভাঙবে না
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['ID', 'Date', 'Type', 'Title', 'Party', 'Account', 'Debit', 'Credit', 'Receipt', 'Note']);

            $q->orderBy('transactions_date')
                ->orderBy('id')
                ->chunk(500, function ($rows) use ($out) {
                    foreach ($rows as $tx) {
                        $typeName = data_get($tx, 'type.name') ?? ('Type #' . $tx->transactions_type_id);
                        $party    = $this->partyName($tx);
                        $account  = data_get($tx, 'account.name') ?? '';

                        $title = trim((string)($tx->c_s_1 ?? ''));
                        $note  = trim((string)($tx->note ?? ''));

                        if ($title === '' && $note !== '') {
                            $title = $note;
                            $note  = '';
                        }

                        fputcsv($out, [
                            $tx->id,
                            $tx->transactions_date,
                            $typeName,
                            $title,
                            $party,
                            $account,
                            (float) ($tx->debit ?? 0),
                            (float) ($tx->credit ?? 0),
                            $tx->recipt_no,
                            $note,
                        ]);
                    }
                });

            fclose($out);
        }, $filename);
    }

    // =========================
    // ✅ Print View (Browser Print → Save as PDF)
    // Bangla 100% safe
    // =========================
    public function transactionsPrint(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);
        $q = $this->buildTransactionsQuery($request, $from, $to);

        $totalDebit  = (float) (clone $q)->sum('debit');
        $totalCredit = (float) (clone $q)->sum('credit');

        // ✅ Print should not paginate
        $rows = $q->orderBy('transactions_date')->orderBy('id')->get();

        $month = $request->input('month');
        $year  = $request->input('year');

        return view('reports.transactions_print', compact(
            'rows',
            'from',
            'to',
            'month',
            'year',
            'totalDebit',
            'totalCredit'
        ));
    }

    // =========================
    // ✅ PDF Endpoint (same as print view)
    // User will "Save as PDF" from browser.
    // =========================
    public function transactionsPdf(Request $request)
    {
        return $this->transactionsPrint($request);
    }
}