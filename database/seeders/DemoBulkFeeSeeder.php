<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoBulkFeeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $onlyExisting = function (string $table, array $data) {
            $cols = Schema::getColumnListing($table);
            return array_intersect_key($data, array_flip($cols));
        };

        // =========================
        // 1) Academic Years
        // =========================
        $academyTable = 'add_academies';

        if (Schema::hasTable($academyTable)) {
            $academyKeyCol = Schema::hasColumn($academyTable, 'year')
                ? 'year'
                : (Schema::hasColumn($academyTable, 'name') ? 'name' : null);

            if ($academyKeyCol) {
                foreach ([2001, 2024, 2025] as $y) {
                    DB::table($academyTable)->updateOrInsert(
                        [$academyKeyCol => $y],
                        $onlyExisting($academyTable, [
                            $academyKeyCol => $y,
                            'isActived'    => 1,
                            'isDeleted'    => 0,
                            'created_at'   => $now,
                            'updated_at'   => $now,
                        ])
                    );
                }
            }
        }

        // Get one academy id (use 2001 as demo)
        $academyId = Schema::hasTable($academyTable)
            ? (int) DB::table($academyTable)->where(
                Schema::hasColumn($academyTable, 'year') ? 'year' : 'id',
                Schema::hasColumn($academyTable, 'year') ? 2001 : 1
            )->value('id')
            : 1;

        // =========================
        // 2) Months (Jan-Dec)
        // =========================
        $monthTable = 'add_months';
        if (Schema::hasTable($monthTable)) {
            $months = [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ];

            foreach ($months as $idx => $name) {
                // unique key choose: name/month/title
                $keyCol = Schema::hasColumn($monthTable, 'name') ? 'name' : (Schema::hasColumn($monthTable, 'month') ? 'month' : null);
                if (!$keyCol) continue;

                $unique = [$keyCol => ($keyCol === 'month' ? ($idx + 1) : $name)];

                DB::table($monthTable)->updateOrInsert(
                    $unique,
                    $onlyExisting($monthTable, array_merge($unique, [
                        // if both exist, fill both
                        'name'       => $name,
                        'month'      => $idx + 1,
                        'isActived'  => 1,
                        'isDeleted'  => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]))
                );
            }
        }

        // =========================
        // 3) Classes (1-10)
        // =========================
        $classTable = 'add_classes';
        if (Schema::hasTable($classTable)) {
            for ($i = 1; $i <= 10; $i++) {
                $label = "Class {$i}";
                $keyCol = Schema::hasColumn($classTable, 'name') ? 'name' : (Schema::hasColumn($classTable, 'class_name') ? 'class_name' : null);
                if (!$keyCol) continue;

                DB::table($classTable)->updateOrInsert(
                    [$keyCol => $label],
                    $onlyExisting($classTable, [
                        $keyCol      => $label,
                        'name'       => $label,
                        'class_name' => $label,
                        'isActived'  => 1,
                        'isDeleted'  => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])
                );
            }
        }

        // =========================
        // 4) Sections (A/B/C)
        // =========================
        $sectionTable = 'add_sections';
        if (Schema::hasTable($sectionTable)) {
            foreach (['A', 'B', 'C'] as $s) {
                $label = "Section {$s}";
                $keyCol = Schema::hasColumn($sectionTable, 'name') ? 'name' : (Schema::hasColumn($sectionTable, 'section_name') ? 'section_name' : null);
                if (!$keyCol) continue;

                DB::table($sectionTable)->updateOrInsert(
                    [$keyCol => $label],
                    $onlyExisting($sectionTable, [
                        $keyCol        => $label,
                        'name'         => $label,
                        'section_name' => $label,
                        'isActived'    => 1,
                        'isDeleted'    => 0,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ])
                );
            }
        }

        // pick demo class & section ids
        $classId = Schema::hasTable($classTable) ? (int) DB::table($classTable)->orderBy('id')->value('id') : 1;
        $sectionId = Schema::hasTable($sectionTable) ? (int) DB::table($sectionTable)->orderBy('id')->value('id') : 1;

        // =========================
        // 5) Accounts
        // =========================
        $accTable = 'accounts';
        if (Schema::hasTable($accTable)) {
            $accNameCol = Schema::hasColumn($accTable, 'name') ? 'name' : null;
            if ($accNameCol) {
                DB::table($accTable)->updateOrInsert(
                    [$accNameCol => 'Cash'],
                    $onlyExisting($accTable, [
                        $accNameCol       => 'Cash',
                        'account_details' => 'Cash in hand',
                        'account_number'  => 'CASH-001',
                        'isActived'       => 1,
                        'isDeleted'       => 0,
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ])
                );
            }
        }

        // =========================
        // 6) Transactions Types (key system)
        // =========================
        $typeTable = 'transactions_types';
        if (Schema::hasTable($typeTable) && Schema::hasColumn($typeTable, 'key')) {
            $types = [
                ['key' => 'student_fee',     'name' => 'Student Fee'],
                ['key' => 'donation',        'name' => 'Donation'],
                ['key' => 'loan_taken',      'name' => 'Loan Taken'],
                ['key' => 'loan_repayment',  'name' => 'Loan Repayment'],
                ['key' => 'expense',         'name' => 'Expense'],
                ['key' => 'income',          'name' => 'Income'],
            ];

            foreach ($types as $t) {
                DB::table($typeTable)->updateOrInsert(
                    ['key' => $t['key']],
                    $onlyExisting($typeTable, [
                        'key'        => $t['key'],
                        'name'       => $t['name'],
                        'isActived'  => 1,
                        'isDeleted'  => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])
                );
            }
        }

        // =========================
        // 7) Students (demo)
        // =========================
        $studentTable = 'students';
        if (Schema::hasTable($studentTable)) {

            // 10 demo students (matching your filter columns)
            for ($i = 1; $i <= 10; $i++) {

                $name = "Demo Student {$i}";
                $roll = $i;

                // unique key: roll / student_name / name (যেটা আছে)
                $unique = [];
                if (Schema::hasColumn($studentTable, 'roll')) $unique = ['roll' => $roll];
                elseif (Schema::hasColumn($studentTable, 'roll_no')) $unique = ['roll_no' => $roll];
                elseif (Schema::hasColumn($studentTable, 'student_name')) $unique = ['student_name' => $name];
                elseif (Schema::hasColumn($studentTable, 'name')) $unique = ['name' => $name];
                else $unique = ['id' => 1000 + $i]; // fallback

                DB::table($studentTable)->updateOrInsert(
                    $unique,
                    $onlyExisting($studentTable, array_merge($unique, [
                        // important for your Load Students filter:
                        'academic_year_id' => $academyId,
                        'class_id'         => $classId,
                        'section_id'       => $sectionId,

                        // names
                        'name'         => $name,
                        'student_name' => $name,
                        'full_name'    => $name,

                        // common fields if exist
                        'mobile'     => '017000000' . str_pad((string)$i, 2, '0', STR_PAD_LEFT),
                        'phone'      => '017000000' . str_pad((string)$i, 2, '0', STR_PAD_LEFT),
                        'email'      => "demo{$i}@example.com",
                        'address'    => 'Dhaka',
                        'gender'     => 'Male',

                        'isActived'  => 1,
                        'isDeleted'  => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]))
                );
            }
        }
    }
}
