<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Student;
use App\Models\Donor;
use App\Models\Lender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;

class QuickCreateController extends Controller
{
    /**
     * ✅ One endpoint for Alpine modal: POST /ajax/{entity}
     * entity = students | donors | lenders | accounts | catagories | expens | incomes
     */
    public function store(Request $request, string $entity)
    {
        $entity = Str::lower($entity);

        return match ($entity) {
            'accounts'   => $this->storeAccount($request),
            'students'   => $this->storeStudent($request),
            'donors'     => $this->storeDonor($request),
            'lenders'    => $this->storeLender($request),

            // ✅ NEW (Fix: Expense/Income modal + Add)
            'catagories' => $this->storeSimpleNameTable($request, 'catagories'),
            'expens'     => $this->storeSimpleNameTable($request, 'expens'),
            'incomes'    => $this->storeSimpleNameTable($request, 'incomes'),

            default      => response()->json(['message' => 'Unknown entity'], 422),
        };
    }

    /**
     * ✅ Generic simple insert: catagories/expens/incomes
     * - Accepts JSON: {name: "..."}
     * - Inserts into table (name/title column auto-detect)
     * - Sets isActived/isDeleted if exists
     */
    protected function storeSimpleNameTable(Request $request, string $table)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
        ]);

        if (!Schema::hasTable($table)) {
            return response()->json(['message' => "Table not found: {$table}"], 422);
        }

        // detect name column
        $nameCol = null;
        if (Schema::hasColumn($table, 'name')) {
            $nameCol = 'name';
        } elseif (Schema::hasColumn($table, 'title')) {
            $nameCol = 'title';
        } else {
            return response()->json(['message' => "No name/title column in table: {$table}"], 422);
        }

        $now = now();

        $payload = [
            $nameCol => $data['name'],
        ];

        // flags if exist
        if (Schema::hasColumn($table, 'isActived')) $payload['isActived'] = 1;
        if (Schema::hasColumn($table, 'isDeleted')) $payload['isDeleted'] = 0;

        // common user columns if exist (prevent NOT NULL issues)
        $uid = auth()->id() ?: 1;
        foreach (['users_id', 'user_id', 'created_by_id'] as $col) {
            if (Schema::hasColumn($table, $col) && !array_key_exists($col, $payload)) {
                $payload[$col] = $uid;
            }
        }

        // timestamps if exist
        if (Schema::hasColumn($table, 'created_at')) $payload['created_at'] = $now;
        if (Schema::hasColumn($table, 'updated_at')) $payload['updated_at'] = $now;

        try {
            $id = DB::table($table)->insertGetId($payload);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'DB insert failed',
                'error'   => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'id'   => $id,
            'name' => $data['name'],
        ], 201);
    }

    /**
     * ✅ Account quick create
     * - account_number auto-generate if empty
     * - ensure unique
     * - set balances + isActived/isDeleted
     */
    public function storeAccount(Request $request)
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'account_number'  => ['nullable', 'string', 'max:255'],
            'account_details' => ['nullable', 'string', 'max:255'],
        ]);

        $acc = new Account();

        // Name
        $this->fillIfColumn($acc, 'name', $data['name']);

        // account_number: generate if empty + ensure unique (only if column exists)
        if (Schema::hasColumn($acc->getTable(), 'account_number')) {
            $accountNumber = trim((string)($data['account_number'] ?? ''));

            if ($accountNumber === '') {
                do {
                    $accountNumber = 'AC-' . strtoupper(Str::random(6));
                } while (Account::where('account_number', $accountNumber)->exists());
            } else {
                if (Account::where('account_number', $accountNumber)->exists()) {
                    return response()->json(['message' => 'account_number already exists'], 422);
                }
            }

            $acc->account_number = $accountNumber;
        }

        // account_details
        $details = $data['account_details'] ?? 'Quick added from Transaction Center';
        $this->fillIfColumn($acc, 'account_details', $details);

        // balances + flags (if exist)
        $this->fillIfColumn($acc, 'opening_balance', 0);
        $this->fillIfColumn($acc, 'current_balance', 0);

        $this->fillIfColumn($acc, 'isActived', true);
        $this->fillIfColumn($acc, 'isDeleted', false);

        $acc->save();

        return response()->json([
            'id'   => $acc->id,
            'name' => $acc->name ?? ('Account #' . $acc->id),
        ], 201);
    }

    /**
     * ✅ Student quick create
     * Supports:
     * - Full mode: if first_name provided
     * - Fallback: name only
     */
    public function storeStudent(Request $request)
    {
        // If modal sends first_name, treat as full create
        $isFull = $request->filled('first_name');

        if ($isFull) {
            $data = $request->validate([
                'name'               => ['nullable', 'string', 'max:255'],

                'first_name'         => ['required', 'string', 'max:120'],
                'last_name'          => ['nullable', 'string', 'max:120'],
                'dob'                => ['nullable', 'date'],
                'age'                => ['nullable', 'integer', 'min:0', 'max:200'],
                'roll'               => ['nullable', 'integer', 'min:0'],
                'scholarship_amount' => ['nullable', 'numeric', 'min:0'],

                'mobile'             => ['nullable', 'string', 'max:30'],
                'email'              => ['nullable', 'email', 'max:255'],

                // FKs (nullable)
                'fees_type_id'       => ['nullable', 'exists:add_fess_types,id'],
                'academic_year_id'   => ['nullable', 'exists:add_academies,id'],
                'class_id'           => ['nullable', 'exists:add_classes,id'],
                'section_id'         => ['nullable', 'exists:add_sections,id'],

                'isActived'          => ['nullable', 'boolean'],
                'photo'              => ['nullable', 'image', 'max:2048'],
            ]);

            $student = new Student();

            // display name fallback
            $autoName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
            $displayName = trim((string)($data['name'] ?? ''));
            if ($displayName === '') $displayName = $autoName;
            if ($displayName === '') $displayName = 'Student';

            $this->fillIfColumn($student, 'full_name', $displayName);
            $this->fillIfColumn($student, 'name', $displayName);
            $this->fillIfColumn($student, 'student_name', $displayName);

            $this->fillIfColumn($student, 'first_name', $data['first_name'] ?? null);
            $this->fillIfColumn($student, 'last_name', $data['last_name'] ?? null);

            $this->fillIfColumn($student, 'dob', $data['dob'] ?? null);
            $this->fillIfColumn($student, 'age', $data['age'] ?? null);
            $this->fillIfColumn($student, 'roll', $data['roll'] ?? null);
            $this->fillIfColumn($student, 'scholarship_amount', $data['scholarship_amount'] ?? null);

            $this->fillIfColumn($student, 'mobile', $data['mobile'] ?? null);
            $this->fillIfColumn($student, 'email', $data['email'] ?? null);

            $this->fillIfColumn($student, 'fees_type_id', $data['fees_type_id'] ?? null);
            $this->fillIfColumn($student, 'academic_year_id', $data['academic_year_id'] ?? null);
            $this->fillIfColumn($student, 'class_id', $data['class_id'] ?? null);
            $this->fillIfColumn($student, 'section_id', $data['section_id'] ?? null);

            $this->fillIfColumn($student, 'isActived', array_key_exists('isActived', $data) ? (bool)$data['isActived'] : true);
            $this->fillIfColumn($student, 'isDeleted', false);

            // photo store (optional)
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('students', 'public');

                $this->fillIfColumn($student, 'photo', $path);
                $this->fillIfColumn($student, 'photo_path', $path);
                $this->fillIfColumn($student, 'image', $path);
                $this->fillIfColumn($student, 'image_path', $path);
            }

            $student->save();

            return response()->json([
                'id'               => $student->id,
                'name'             => $student->full_name ?? $displayName,

                'roll'             => $student->roll ?? ($data['roll'] ?? null),
                'class_id'         => $student->class_id ?? ($data['class_id'] ?? null),
                'section_id'       => $student->section_id ?? ($data['section_id'] ?? null),
                'academic_year_id' => $student->academic_year_id ?? ($data['academic_year_id'] ?? null),
                'fees_type_id'     => $student->fees_type_id ?? ($data['fees_type_id'] ?? null),
            ], 201);
        }

        // ✅ fallback: name only
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'email'  => ['nullable', 'email', 'max:255'],
        ]);

        $s = new Student();
        $this->fillIfColumn($s, 'full_name', $data['name']);
        $this->fillIfColumn($s, 'name', $data['name']);
        $this->fillIfColumn($s, 'student_name', $data['name']);

        $this->fillIfColumn($s, 'mobile', $data['mobile'] ?? null);
        $this->fillIfColumn($s, 'email', $data['email'] ?? null);

        $this->fillIfColumn($s, 'isActived', true);
        $this->fillIfColumn($s, 'isDeleted', false);

        $s->save();

        return response()->json([
            'id'               => $s->id,
            'name'             => $s->full_name ?? ('Student #' . $s->id),
            'roll'             => $s->roll ?? null,
            'class_id'         => $s->class_id ?? null,
            'section_id'       => $s->section_id ?? null,
            'academic_year_id' => $s->academic_year_id ?? null,
            'fees_type_id'     => $s->fees_type_id ?? null,
        ], 201);
    }

    /**
     * ✅ Donor quick create
     */
    public function storeDonor(Request $request)
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'email'  => ['nullable', 'email', 'max:255'],
        ]);

        $d = new Donor();

        $this->fillIfColumn($d, 'name', $data['name']);
        $this->fillIfColumn($d, 'donor_name', $data['name']);
        $this->fillIfColumn($d, 'doner_name', $data['name']);

        $this->fillIfColumn($d, 'mobile', $data['mobile'] ?? null);
        $this->fillIfColumn($d, 'email', $data['email'] ?? null);

        $this->fillIfColumn($d, 'isActived', true);
        $this->fillIfColumn($d, 'isDeleted', false);

        $d->save();

        return response()->json([
            'id'   => $d->id,
            'name' => $d->name ?? ('Donor #' . $d->id),
        ], 201);
    }

    /**
     * ✅ Lender quick create
     */
    public function storeLender(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'phone'       => ['required', 'string', 'max:50'],
            'email'       => ['required', 'email', 'max:255'],
            'address'     => ['required', 'string', 'max:255'],
            'bank_detils' => ['required', 'string', 'max:255'],
        ]);

        $l = new Lender();

        $this->fillIfColumn($l, 'name', $data['name']);
        $this->fillIfColumn($l, 'lender_name', $data['name']);

        $this->fillIfColumn($l, 'phone', $data['phone']);
        $this->fillIfColumn($l, 'email', $data['email']);
        $this->fillIfColumn($l, 'address', $data['address']);
        $this->fillIfColumn($l, 'bank_detils', $data['bank_detils']);

        // required FK users_id (if exists)
        $this->fillIfColumn($l, 'users_id', auth()->id() ?: 1);

        $this->fillIfColumn($l, 'isActived', true);
        $this->fillIfColumn($l, 'isDeleted', false);

        $l->save();

        return response()->json([
            'id'   => $l->id,
            'name' => $l->name ?? ('Lender #' . $l->id),
        ], 201);
    }

    /**
     * ✅ Only set if column exists in DB table (safe across mismatched schemas)
     */
    protected function fillIfColumn($model, string $column, $value): void
    {
        if ($value === null) return;

        $table = $model->getTable();
        if (Schema::hasColumn($table, $column)) {
            $model->{$column} = $value;
        }
    }
}