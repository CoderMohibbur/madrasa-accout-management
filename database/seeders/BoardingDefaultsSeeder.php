<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BoardingDefaultsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1) Category
        $catName = 'Boarding/Hostel';

        $catId = DB::table('catagories')->where('name', $catName)->value('id');
        if (!$catId) {
            $catId = DB::table('catagories')->insertGetId([
                'name' => $catName,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 2) Expense Heads
        $heads = [
            'Hostel Food',
            'Bazar',
            'Gas',
            'Electricity',
            'Staff Salary',
            'Repair',
            'Water',
            'Misc Hostel Expense',
        ];

        foreach ($heads as $h) {
            $exists = DB::table('expens')
                ->where('name', $h)
                ->where('catagory_id', $catId)
                ->where('isDeleted', 0)
                ->exists();

            if (!$exists) {
                DB::table('expens')->insert([
                    'name' => $h,
                    'catagory_id' => $catId,
                    'isActived' => 1,
                    'isDeleted' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}