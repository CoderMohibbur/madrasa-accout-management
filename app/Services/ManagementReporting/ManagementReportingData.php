<?php

namespace App\Services\ManagementReporting;

use App\Models\Receipt;
use App\Models\StudentFeeInvoice;
use App\Models\Transactions;
use App\Models\TransactionsType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class ManagementReportingData
{
    public function resolveRange(?string $from, ?string $to): array
    {
        try {
            $start = $from ? Carbon::parse($from)->startOfDay() : now()->startOfMonth()->startOfDay();
        } catch (Throwable) {
            $start = now()->startOfMonth()->startOfDay();
        }

        try {
            $end = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();
        } catch (Throwable) {
            $end = now()->endOfDay();
        }

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [
            'from' => $start->toDateString(),
            'to' => $end->toDateString(),
            'from_at' => $start,
            'to_at' => $end,
        ];
    }

    public function inflowBreakdown(string $from, string $to): Collection
    {
        $base = $this->transactionBaseQuery($from, $to);

        $sources = collect([
            ['key' => 'student_fee', 'label' => 'Student Fees'],
            ['key' => 'donation', 'label' => 'Donations'],
            ['key' => 'income', 'label' => 'Other Income'],
            ['key' => 'loan_taken', 'label' => 'Loans Taken'],
        ])->map(function (array $source) use ($base): array {
            $typeId = $this->typeId($source['key']);

            if (! $typeId) {
                return [
                    'key' => $source['key'],
                    'label' => $source['label'],
                    'rows' => 0,
                    'total' => 0.0,
                    'share' => 0.0,
                ];
            }

            $query = (clone $base)
                ->where('transactions_type_id', $typeId)
                ->where('credit', '>', 0);

            return [
                'key' => $source['key'],
                'label' => $source['label'],
                'rows' => (clone $query)->count(),
                'total' => (float) (clone $query)->sum('credit'),
                'share' => 0.0,
            ];
        });

        $total = (float) $sources->sum('total');

        return $sources->map(function (array $source) use ($total): array {
            $source['share'] = $total > 0 ? round(($source['total'] / $total) * 100, 2) : 0.0;

            return $source;
        });
    }

    public function invoiceSummary(string $from, string $to): array
    {
        $query = $this->invoiceQuery($from, $to);

        return [
            'invoice_count' => (clone $query)->count(),
            'open_count' => (clone $query)->where('balance_amount', '>', 0)->count(),
            'overdue_count' => (clone $query)
                ->whereDate('due_at', '<', now()->toDateString())
                ->where('balance_amount', '>', 0)
                ->count(),
            'paid_count' => (clone $query)->where('balance_amount', '<=', 0)->count(),
            'billed_total' => (float) (clone $query)->sum('total_amount'),
            'outstanding_total' => (float) (clone $query)->sum('balance_amount'),
        ];
    }

    public function openInvoices(string $from, string $to): Collection
    {
        return $this->invoiceQuery($from, $to)
            ->where('balance_amount', '>', 0)
            ->orderBy('due_at')
            ->orderBy('id')
            ->limit(8)
            ->get();
    }

    public function receiptSummary(Carbon $fromAt, Carbon $toAt): array
    {
        $query = $this->receiptQuery($fromAt, $toAt);

        return [
            'receipt_count' => (clone $query)->count(),
            'receipt_total' => (float) (clone $query)->sum('amount'),
        ];
    }

    public function recentReceipts(Carbon $fromAt, Carbon $toAt): Collection
    {
        return $this->receiptQuery($fromAt, $toAt)
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();
    }

    private function invoiceQuery(string $from, string $to): Builder
    {
        return StudentFeeInvoice::query()
            ->with(['student.class', 'student.section'])
            ->where(function (Builder $query) use ($from, $to): void {
                $query
                    ->whereBetween('issued_at', [$from, $to])
                    ->orWhere(function (Builder $fallbackQuery) use ($from, $to): void {
                        $fallbackQuery
                            ->whereNull('issued_at')
                            ->whereDate('created_at', '>=', $from)
                            ->whereDate('created_at', '<=', $to);
                    });
            });
    }

    private function receiptQuery(Carbon $fromAt, Carbon $toAt): Builder
    {
        return Receipt::query()
            ->with('payment')
            ->where(function (Builder $query) use ($fromAt, $toAt): void {
                $query
                    ->whereBetween('issued_at', [$fromAt, $toAt])
                    ->orWhere(function (Builder $fallbackQuery) use ($fromAt, $toAt): void {
                        $fallbackQuery
                            ->whereNull('issued_at')
                            ->whereBetween('created_at', [$fromAt, $toAt]);
                    });
            });
    }

    private function transactionBaseQuery(string $from, string $to): Builder
    {
        $query = Transactions::query()
            ->whereDate('transactions_date', '>=', $from)
            ->whereDate('transactions_date', '<=', $to);

        if (Schema::hasColumn('transactions', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        if (Schema::hasColumn('transactions', 'isDeleted')) {
            $query->where('isDeleted', 0);
        }

        if (Schema::hasColumn('transactions', 'isActived')) {
            $query->where('isActived', 1);
        }

        return $query;
    }

    private function typeId(string $key): ?int
    {
        try {
            return TransactionsType::idByKey($key);
        } catch (Throwable) {
            // Fall through to the name-based compatibility lookup.
        }

        $map = [
            'student_fee' => ['Student Fee', 'Student Fees', 'Student Fess', 'Student Fesses'],
            'donation' => ['Donation', 'Donar', 'Doner', 'Doner Donation'],
            'income' => ['Income'],
            'expense' => ['Expense', 'Expens'],
            'loan_taken' => ['Loan', 'Loan Taken', 'Loan Take'],
            'loan_repayment' => ['Repayment', 'Loan Repayment', 'Loan Pay'],
        ];

        $names = $map[$key] ?? [];

        if ($names !== []) {
            $id = TransactionsType::query()->whereIn('name', $names)->value('id');

            return $id ? (int) $id : null;
        }

        $id = TransactionsType::query()
            ->where('name', 'like', '%' . Str::headline($key) . '%')
            ->value('id');

        return $id ? (int) $id : null;
    }
}
