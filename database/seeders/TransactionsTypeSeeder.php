<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionsType;

class TransactionsTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Student Fee',    'key' => 'student_fee'],
            ['name' => 'Donation',       'key' => 'donation'],
            ['name' => 'Loan Taken',     'key' => 'loan_taken'],
            ['name' => 'Loan Repayment', 'key' => 'loan_repayment'],
            ['name' => 'Expense',        'key' => 'expense'],
            ['name' => 'Income',         'key' => 'income'],
        ];

        foreach ($types as $t) {
            TransactionsType::updateOrCreate(
                ['key' => $t['key']],
                ['name' => $t['name'], 'isActived' => true, 'isDeleted' => false]
            );
        }
    }
}
