<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MadrasaDemoSeeder extends Seeder
{
    private array $colsCache = [];
    private array $showColsCache = [];

    public function run(): void
    {
        // Users empty থাকলে admin create (FK safe)
        $this->ensureAdminUser();

        // Master data
        $this->createAccounts();
        $this->createAcademies();
        $this->createMonths();
        $this->createClasses();
        $this->createSections();
        $this->createFeesTypes();

        // Dependent data
        $this->createRegistrationFees(); // depends on classes
        $this->createDonors();
        $this->createLenders();
        $this->createStudents(); // depends on academies/classes/sections/feesTypes

        // Transaction setup
        $this->createTransactionTypes();
        $this->createTransactions(); // depends on all above
    }

    // =========================
    // Core helpers
    // =========================

    private function cols(string $table): array
    {
        if (!isset($this->colsCache[$table])) {
            $this->colsCache[$table] = Schema::hasTable($table)
                ? Schema::getColumnListing($table)
                : [];
        }
        return $this->colsCache[$table];
    }

    private function showCols(string $table): array
    {
        if (!isset($this->showColsCache[$table])) {
            if (!Schema::hasTable($table)) {
                $this->showColsCache[$table] = [];
            } else {
                $this->showColsCache[$table] = DB::select("SHOW COLUMNS FROM `$table`");
            }
        }
        return $this->showColsCache[$table];
    }

    private function onlyExistingCols(string $table, array $data): array
    {
        if (!Schema::hasTable($table)) return [];
        $cols = array_flip($this->cols($table));
        return array_intersect_key($data, $cols);
    }

    private function withFlagsAndTimestamps(string $table, array $row): array
    {
        $cols = $this->cols($table);

        if (in_array('isActived', $cols, true) && !array_key_exists('isActived', $row)) $row['isActived'] = 1;
        if (in_array('isDeleted', $cols, true) && !array_key_exists('isDeleted', $row)) $row['isDeleted'] = 0;

        if (in_array('created_at', $cols, true) && !array_key_exists('created_at', $row)) $row['created_at'] = now();
        if (in_array('updated_at', $cols, true) && !array_key_exists('updated_at', $row)) $row['updated_at'] = now();

        return $row;
    }

    private function fkDefaultId(string $field): ?int
    {
        // known FK map (project specific)
        $map = [
            'created_by_id'        => 'users',
            'account_id'           => 'accounts',
            'transactions_type_id' => 'transactions_types',

            'student_id' => 'students',
            'doner_id'   => 'donors',
            'lender_id'  => 'lenders',

            'academic_year_id' => 'add_academies',
            'months_id'        => 'add_months',
            'class_id'         => 'add_classes',
            'section_id'       => 'add_sections',
            'fees_type_id'     => 'add_fess_types',
            'fess_type_id'     => 'add_fess_types',

            'catagory_id' => 'catagories',
            'income_id'   => 'incomes',
            'expens_id'   => 'expens',
        ];

        $table = $map[$field] ?? null;
        if (!$table || !Schema::hasTable($table)) return null;

        $id = DB::table($table)->orderBy('id')->value('id');
        return $id ? (int)$id : null;
    }

    private function defaultForType(string $type, string $field)
    {
        $t = strtolower($type);

        // FK guess
        if (str_ends_with($field, '_id')) {
            $id = $this->fkDefaultId($field);
            return $id ?? 1;
        }

        if (str_contains($t, 'int') || str_contains($t, 'decimal') || str_contains($t, 'float') || str_contains($t, 'double')) {
            return 0;
        }

        if (str_contains($t, 'tinyint(1)')) return 0;

        if (str_contains($t, 'date') && !str_contains($t, 'datetime')) return Carbon::now()->toDateString();
        if (str_contains($t, 'datetime') || str_contains($t, 'timestamp')) return Carbon::now()->toDateTimeString();

        if (str_starts_with($t, 'enum(')) {
            // enum('a','b') -> pick first
            preg_match_all("/'([^']+)'/", $t, $m);
            return $m[1][0] ?? '';
        }

        if (str_contains($t, 'json')) return json_encode([]);

        return '';
    }

    private function fillRequired(string $table, array $row): array
    {
        foreach ($this->showCols($table) as $c) {
            $field = $c->Field;
            $null  = $c->Null;     // 'YES'/'NO'
            $def   = $c->Default;  // null or value
            $extra = $c->Extra ?? '';
            $type  = $c->Type ?? 'varchar(255)';

            if ($field === 'id') continue;
            if (str_contains((string)$extra, 'auto_increment')) continue;

            if ($null === 'NO' && $def === null && !array_key_exists($field, $row)) {
                $row[$field] = $this->defaultForType((string)$type, (string)$field);
            }
        }
        return $row;
    }

    private function normalizeRow(string $table, array $row): array
    {
        $row = $this->withFlagsAndTimestamps($table, $row);
        $row = $this->onlyExistingCols($table, $row);
        $row = $this->fillRequired($table, $row);
        return $row;
    }

    private function upsertGetId(string $table, array $keys, array $data): int
    {
        if (!Schema::hasTable($table)) return 0;

        $keys = $this->onlyExistingCols($table, $keys);
        $data = $this->normalizeRow($table, array_merge($keys, $data));

        DB::table($table)->updateOrInsert($keys, $data);

        $id = DB::table($table)->where($keys)->value('id');
        return $id ? (int)$id : 0;
    }

    private function firstId(string $table): int
    {
        if (!Schema::hasTable($table)) return 0;
        $id = DB::table($table)->orderBy('id')->value('id');
        return $id ? (int)$id : 0;
    }

    // =========================
    // Seed: Users
    // =========================

    private function ensureAdminUser(): void
    {
        if (!Schema::hasTable('users')) return;

        if (DB::table('users')->count() > 0) return;

        $table = 'users';
        $row = [
            'name'  => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];

        $row = $this->normalizeRow($table, $row);
        DB::table($table)->insert($row);
    }

    // =========================
    // Seed: Accounts
    // =========================

    private function createAccounts(): void
    {
        if (!Schema::hasTable('accounts')) return;

        $table = 'accounts';

        $items = [
            ['name' => 'Cash', 'account_number' => 'AC-0001', 'account_details' => 'Cash Account'],
            ['name' => 'Bank', 'account_number' => 'AC-0002', 'account_details' => 'Bank Account'],
        ];

        foreach ($items as $it) {
            // ✅ ensure opening balance always present if column exists
            $it['opening_balance'] = 0;
            $it['opening_blance']  = 0; // typo-safe (jodi thake)

            $key = [];
            if (in_array('account_number', $this->cols($table), true)) $key['account_number'] = $it['account_number'];
            else $key['name'] = $it['name'];

            $this->upsertGetId($table, $key, $it);
        }
    }

    // =========================
    // Seed: Academies (Years)
    // =========================

    private function createAcademies(): void
    {
        if (!Schema::hasTable('add_academies')) return;

        $table = 'add_academies';
        $start = (int)date('Y');

        for ($i = 0; $i < 3; $i++) {
            $y  = $start + $i;
            $y2 = $y + 1;

            $row = [
                'year' => $y,
                'academic_years' => $y . '-' . $y2,  // ✅ FIX: required field
                'academic_year'  => $y . '-' . $y2,  // alt name safe
                'name' => $y . '-' . $y2,
                'title' => $y . '-' . $y2,
            ];

            $key = [];
            if (in_array('year', $this->cols($table), true)) $key['year'] = $y;
            elseif (in_array('academic_years', $this->cols($table), true)) $key['academic_years'] = $y . '-' . $y2;
            else $key['name'] = $y . '-' . $y2;

            $this->upsertGetId($table, $key, $row);
        }
    }

    // =========================
    // Seed: Months
    // =========================

    private function createMonths(): void
    {
        if (!Schema::hasTable('add_months')) return;

        $table = 'add_months';
        $months = [
            'January','February','March','April','May','June',
            'July','August','September','October','November','December'
        ];

        foreach ($months as $m) {
            $row = ['name' => $m, 'title' => $m, 'month' => $m];

            $key = [];
            if (in_array('name', $this->cols($table), true)) $key['name'] = $m;
            else $key['title'] = $m;

            $this->upsertGetId($table, $key, $row);
        }
    }

    // =========================
    // Seed: Classes
    // =========================

    private function createClasses(): void
    {
        if (!Schema::hasTable('add_classes')) return;

        $table = 'add_classes';
        for ($i = 1; $i <= 5; $i++) {
            $name = 'Class ' . $i;
            $row = ['name' => $name, 'title' => $name, 'class_name' => $name];

            $key = [];
            if (in_array('name', $this->cols($table), true)) $key['name'] = $name;
            else $key['title'] = $name;

            $this->upsertGetId($table, $key, $row);
        }
    }

    // =========================
    // Seed: Sections
    // =========================

    private function createSections(): void
    {
        if (!Schema::hasTable('add_sections')) return;

        $table = 'add_sections';
        $sections = ['A', 'B', 'C'];

        foreach ($sections as $s) {
            $name = 'Section ' . $s;
            $row  = ['name' => $name, 'title' => $name];

            $key = [];
            if (in_array('name', $this->cols($table), true)) $key['name'] = $name;
            else $key['title'] = $name;

            $this->upsertGetId($table, $key, $row);
        }
    }

    // =========================
    // Seed: Fees Types
    // =========================

    private function createFeesTypes(): void
    {
        if (!Schema::hasTable('add_fess_types')) return;

        $table = 'add_fess_types';
        $types = ['Regular', 'Scholarship', 'Boarding'];

        foreach ($types as $t) {
            $row = ['name' => $t, 'title' => $t];

            $key = [];
            if (in_array('name', $this->cols($table), true)) $key['name'] = $t;
            else $key['title'] = $t;

            $this->upsertGetId($table, $key, $row);
        }
    }

    // =========================
    // Seed: Registration Fees (per class)
    // =========================

    private function createRegistrationFees(): void
    {
        if (!Schema::hasTable('add_registration_fesses')) return;
        if (!Schema::hasTable('add_classes')) return;

        $table = 'add_registration_fesses';
        $classIds = DB::table('add_classes')->orderBy('id')->pluck('id')->toArray();

        foreach ($classIds as $idx => $cid) {
            $monthly = 500 + ($idx * 50);
            $boarding = 200 + ($idx * 20);
            $management = 100 + ($idx * 10);
            $exam = 50 + ($idx * 5);
            $other = 25 + ($idx * 3);

            $row = [
                'class_id' => $cid,
                'name' => 'Class ' . ($idx + 1) . ' Default',

                // ✅ match your migration columns
                'monthly_fee' => $monthly,
                'boarding_fee' => $boarding,
                'management_fee' => $management,
                'examination_fee' => $exam,
                'other' => $other,
            ];

            $this->upsertGetId($table, ['class_id' => $cid], $row);
        }
    }

    // =========================
    // Seed: Donors / Lenders
    // =========================

    private function createDonors(): void
    {
        if (!Schema::hasTable('donors')) return;

        $table = 'donors';
        $items = ['Rahim Donor', 'Karim Donor', 'Salam Donor'];

        foreach ($items as $name) {
            $row = [
                'name' => $name,
                'phone' => '017' . rand(10000000, 99999999),
                'address' => 'Dhaka',
            ];
            $key = in_array('name', $this->cols($table), true) ? ['name' => $name] : ['id' => null];
            $this->upsertGetId($table, $key, $row);
        }
    }

    private function createLenders(): void
    {
        if (!Schema::hasTable('lenders')) return;

        $table = 'lenders';
        $items = ['Rahim Lender', 'Karim Lender'];

        foreach ($items as $name) {
            $row = [
                'name' => $name,
                'phone' => '018' . rand(10000000, 99999999),
                'address' => 'Dhaka',
            ];
            $key = in_array('name', $this->cols($table), true) ? ['name' => $name] : ['id' => null];
            $this->upsertGetId($table, $key, $row);
        }
    }

    // =========================
    // Seed: Students
    // =========================

    private function createStudents(): void
    {
        if (!Schema::hasTable('students')) return;

        $table = 'students';

        $classId   = $this->firstId('add_classes');
        $sectionId = $this->firstId('add_sections');
        $academyId = $this->firstId('add_academies');
        $feesTypeId = $this->firstId('add_fess_types');

        for ($i = 1; $i <= 10; $i++) {
            $row = [
                'name' => 'Student ' . $i,
                'roll' => $i,
                'student_book_number' => 'B-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT),

                // common FK fields
                'class_id' => $classId ?: null,
                'section_id' => $sectionId ?: null,
                'academic_year_id' => $academyId ?: null,
                'fees_type_id' => $feesTypeId ?: null,
                'fess_type_id' => $feesTypeId ?: null,

                'phone' => '019' . rand(10000000, 99999999),
                'address' => 'Dhaka',
            ];

            $key = [];
            if (in_array('student_book_number', $this->cols($table), true)) $key['student_book_number'] = $row['student_book_number'];
            else $key['name'] = $row['name'];

            $this->upsertGetId($table, $key, $row);
        }
    }

    // =========================
    // Seed: Transaction Types
    // =========================

    private function createTransactionTypes(): void
    {
        if (!Schema::hasTable('transactions_types')) return;

        $table = 'transactions_types';
        $types = [
            ['key' => 'student_fee', 'name' => 'Student Fee'],
            ['key' => 'donation', 'name' => 'Donation'],
            ['key' => 'income', 'name' => 'Income'],
            ['key' => 'expense', 'name' => 'Expense'],
            ['key' => 'loan_taken', 'name' => 'Loan Taken'],
            ['key' => 'loan_repayment', 'name' => 'Loan Repayment'],
        ];

        foreach ($types as $t) {
            $row = [
                'key' => $t['key'],
                'name' => $t['name'],
                'title' => $t['name'],
            ];

            $key = [];
            if (in_array('key', $this->cols($table), true)) $key['key'] = $t['key'];
            else $key['name'] = $t['name'];

            $this->upsertGetId($table, $key, $row);
        }
    }

    private function typeIdByKey(string $key): int
    {
        if (!Schema::hasTable('transactions_types')) return 0;
        $table = 'transactions_types';

        if (in_array('key', $this->cols($table), true)) {
            return (int) (DB::table($table)->where('key', $key)->value('id') ?? 0);
        }

        // fallback by name
        $map = [
            'student_fee' => 'Student Fee',
            'donation' => 'Donation',
            'income' => 'Income',
            'expense' => 'Expense',
            'loan_taken' => 'Loan Taken',
            'loan_repayment' => 'Loan Repayment',
        ];
        $name = $map[$key] ?? null;
        if (!$name) return 0;

        return (int) (DB::table($table)->where('name', $name)->value('id') ?? 0);
    }

    // =========================
    // Seed: Transactions
    // =========================

    private function splitLedger(string $typeKey, float $amount): array
    {
        // your controller logic aligned:
        return match ($typeKey) {
            'expense' => ['debit' => $amount, 'credit' => 0],
            'loan_repayment' => ['debit' => $amount, 'credit' => 0],
            default => ['debit' => 0, 'credit' => $amount], // student_fee, donation, income, loan_taken
        };
    }

    private function createTransactions(): void
    {
        if (!Schema::hasTable('transactions')) return;

        $table = 'transactions';

        $createdBy = $this->firstId('users') ?: 1;
        $accountId = $this->firstId('accounts');
        $studentId = $this->firstId('students');
        $donorId   = $this->firstId('donors');
        $lenderId  = $this->firstId('lenders');

        $monthId   = $this->firstId('add_months');
        $academyId = $this->firstId('add_academies');
        $classId   = $this->firstId('add_classes');
        $sectionId = $this->firstId('add_sections');
        $feesTypeId = $this->firstId('add_fess_types');

        // registration fee row (optional)
        $reg = Schema::hasTable('add_registration_fesses')
            ? DB::table('add_registration_fesses')->where('class_id', $classId)->orderByDesc('id')->first()
            : null;

        $mf = $reg?->monthly_fee ?? 500;
        $bf = $reg?->boarding_fee ?? 200;
        $mg = $reg?->management_fee ?? 100;
        $ex = $reg?->examination_fee ?? 50;
        $ot = $reg?->other ?? 25;
        $total = (float)$mf + (float)$bf + (float)$mg + (float)$ex + (float)$ot;

        // 1) Student fee
        $typeId = $this->typeIdByKey('student_fee');
        $split = $this->splitLedger('student_fee', $total);

        $row = [
            'transactions_type_id' => $typeId,
            'account_id' => $accountId,
            'transactions_date' => now()->toDateString(),
            'created_by_id' => $createdBy,
            'student_id' => $studentId,

            'months_id' => $monthId,
            'academic_year_id' => $academyId,
            'class_id' => $classId,
            'section_id' => $sectionId,
            'fess_type_id' => $feesTypeId,

            'monthly_fees' => $mf,
            'boarding_fees' => $bf,
            'management_fees' => $mg,
            'exam_fees' => $ex,
            'others_fees' => $ot,
            'total_fees' => $total,

            'note' => 'Student Fee',
            'recipt_no' => 'R-' . rand(1000, 9999),

            'debit' => $split['debit'],
            'credit' => $split['credit'],
            'amount' => $total,
        ];

        DB::table($table)->insert($this->normalizeRow($table, $row));

        // 2) Donation
        $donAmt = 1000;
        $typeId = $this->typeIdByKey('donation');
        $split = $this->splitLedger('donation', $donAmt);

        $row = [
            'transactions_type_id' => $typeId,
            'account_id' => $accountId,
            'transactions_date' => now()->toDateString(),
            'created_by_id' => $createdBy,
            'doner_id' => $donorId,
            'note' => 'Donation',
            'c_s_1' => 'Donation',
            'recipt_no' => 'D-' . rand(1000, 9999),
            'debit' => $split['debit'],
            'credit' => $split['credit'],
            'amount' => $donAmt,
        ];

        DB::table($table)->insert($this->normalizeRow($table, $row));

        // 3) Expense
        $expAmt = 300;
        $typeId = $this->typeIdByKey('expense');
        $split = $this->splitLedger('expense', $expAmt);

        $row = [
            'transactions_type_id' => $typeId,
            'account_id' => $accountId,
            'transactions_date' => now()->toDateString(),
            'created_by_id' => $createdBy,
            'note' => 'Expense',
            'c_s_1' => 'Stationary Expense',
            'debit' => $split['debit'],
            'credit' => $split['credit'],
            'amount' => $expAmt,
        ];

        DB::table($table)->insert($this->normalizeRow($table, $row));

        // 4) Income
        $incAmt = 700;
        $typeId = $this->typeIdByKey('income');
        $split = $this->splitLedger('income', $incAmt);

        $row = [
            'transactions_type_id' => $typeId,
            'account_id' => $accountId,
            'transactions_date' => now()->toDateString(),
            'created_by_id' => $createdBy,
            'note' => 'Income',
            'c_s_1' => 'Extra Income',
            'debit' => $split['debit'],
            'credit' => $split['credit'],
            'amount' => $incAmt,
        ];

        DB::table($table)->insert($this->normalizeRow($table, $row));

        // 5) Loan Taken + Repayment
        $loanAmt = 2000;

        $typeId = $this->typeIdByKey('loan_taken');
        $split = $this->splitLedger('loan_taken', $loanAmt);

        $row = [
            'transactions_type_id' => $typeId,
            'account_id' => $accountId,
            'transactions_date' => now()->toDateString(),
            'created_by_id' => $createdBy,
            'lender_id' => $lenderId,
            'note' => 'Loan Taken',
            'c_s_1' => 'Loan Taken',
            'debit' => $split['debit'],
            'credit' => $split['credit'],
            'amount' => $loanAmt,
        ];
        DB::table($table)->insert($this->normalizeRow($table, $row));

        $repAmt = 500;
        $typeId = $this->typeIdByKey('loan_repayment');
        $split = $this->splitLedger('loan_repayment', $repAmt);

        $row = [
            'transactions_type_id' => $typeId,
            'account_id' => $accountId,
            'transactions_date' => now()->toDateString(),
            'created_by_id' => $createdBy,
            'lender_id' => $lenderId,
            'note' => 'Loan Repayment',
            'c_s_1' => 'Loan Repayment',
            'debit' => $split['debit'],
            'credit' => $split['credit'],
            'amount' => $repAmt,
        ];
        DB::table($table)->insert($this->normalizeRow($table, $row));
    }
}
