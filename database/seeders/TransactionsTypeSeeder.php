<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionsTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ✅ legacy names match list (আপনার controller এর map থেকে নেওয়া)
        $map = [
            'student_fee' => ['Student Fee', 'Student Fees', 'Student Fess', 'Student Fesses'],
            'donation' => ['Donation', 'Donar', 'Doner', 'Doner Donation'],
            'income' => ['Income'],
            'expense' => ['Expense', 'Expens'],
            'loan_taken' => ['Loan', 'Loan Taken', 'Loan Take'],
            'loan_repayment' => ['Repayment', 'Loan Repayment', 'Loan Pay'],
        ];

        foreach ($map as $key => $names) {

            // 1) key দিয়ে আগে দেখি
            $row = DB::table('transactions_types')->where('key', $key)->first();

            // 2) না থাকলে name দিয়ে দেখি (পুরোনো id preserve হবে)
            if (!$row) {
                $row = DB::table('transactions_types')
                    ->whereIn('name', $names)
                    ->first();
            }

            // 3) এরপরও না থাকলে insert, নাহলে update (id preserve)
            $niceName = Str::of($key)->replace('_', ' ')->title()->toString();

            if ($row) {
                DB::table('transactions_types')
                    ->where('id', $row->id)
                    ->update([
                        'name' => $niceName,
                        'key' => $key,
                        'isActived' => 1,
                        'isDeleted' => 0,
                        'updated_at' => $now,
                    ]);
            } else {
                DB::table('transactions_types')->insert([
                    'name' => $niceName,
                    'key' => $key,
                    'isActived' => 1,
                    'isDeleted' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
