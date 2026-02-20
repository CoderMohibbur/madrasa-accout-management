<?php

namespace App\Exports;

use App\Models\Transactions;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        private array $filters,
        private string $from,
        private string $to
    ) {}

    public function query()
    {
        $q = Transactions::query()
            ->with(['student','donor','lender','account','type'])
            ->whereDate('transactions_date','>=',$this->from)
            ->whereDate('transactions_date','<=',$this->to);

        if (!empty($this->filters['account_id'])) {
            $q->where('account_id', $this->filters['account_id']);
        }

        if (!empty($this->filters['type_id'])) {
            $q->where('transactions_type_id', $this->filters['type_id']);
        }

        if (!empty($this->filters['search'])) {
            $s = trim((string) $this->filters['search']);

            // ✅ group OR conditions to keep date range safe
            $q->where(function ($qq) use ($s) {
                $qq->where('recipt_no', 'like', "%{$s}%")
                    ->orWhere('student_book_number', 'like', "%{$s}%")
                    ->orWhere('note', 'like', "%{$s}%")
                    ->orWhere('c_s_1', 'like', "%{$s}%");

                $qq->orWhereHas('student', function ($q2) use ($s) {
                    $q2->where('full_name','like',"%{$s}%")
                        ->orWhere('name','like',"%{$s}%")
                        ->orWhere('student_name','like',"%{$s}%");
                });

                $qq->orWhereHas('donor', function ($q2) use ($s) {
                    $q2->where('name','like',"%{$s}%")
                        ->orWhere('donor_name','like',"%{$s}%")
                        ->orWhere('doner_name','like',"%{$s}%");
                });

                $qq->orWhereHas('lender', function ($q2) use ($s) {
                    $q2->where('name','like',"%{$s}%")
                        ->orWhere('lender_name','like',"%{$s}%");
                });
            });
        }

        return $q->orderBy('transactions_date')->orderBy('id');
    }

    public function headings(): array
    {
        return ['ID','Date','Type','Title','Party','Account','Debit','Credit','Receipt','Note'];
    }

    public function map($tx): array
    {
        $typeName = data_get($tx,'type.name') ?? ('Type #'.$tx->transactions_type_id);

        $studentName = data_get($tx,'student.full_name')
            ?? data_get($tx,'student.name')
            ?? data_get($tx,'student.student_name');

        $donorName = data_get($tx,'donor.name')
            ?? data_get($tx,'donor.donor_name')
            ?? data_get($tx,'donor.doner_name');

        $lenderName = data_get($tx,'lender.name') ?? data_get($tx,'lender.lender_name');

        $party = (string) ($studentName ?: ($donorName ?: ($lenderName ?: '')));

        $account = (string) (data_get($tx,'account.name') ?? '');

        $title = trim((string)($tx->c_s_1 ?? ''));
        $note  = trim((string)($tx->note ?? ''));

        if ($title === '' && $note !== '') {
            $title = $note;
            $note = '';
        }

        return [
            $tx->id,
            $tx->transactions_date,
            $typeName,
            $title,
            $party,
            $account,
            (float)($tx->debit ?? 0),
            (float)($tx->credit ?? 0),
            $tx->recipt_no,
            $note,
        ];
    }
}