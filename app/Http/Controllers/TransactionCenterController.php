<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\Lender;
use App\Models\Account;
use App\Models\Student;
use App\Models\AddClass;
use App\Models\AddMonth;
use App\Models\AddAcademy;
use App\Models\AddSection;
use App\Models\AddFessType;
use App\Models\AddRegistrationFess;
use App\Models\Transactions;
use App\Models\TransactionsType;
use App\Support\TransactionLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransactionCenterController extends Controller
{
    /**
     * ✅ Phase 1 Core allowed keys
     */
    private const ALLOWED_TYPE_KEYS = [
        'student_fee',
        'donation',
        'income',
        'expense',
        'loan_taken',
        'loan_repayment',
    ];

    /**
     * ✅ Phase-1/2 rule: NEVER hardcode transactions_type_id
     * Always key -> id mapping (safe even if model helper missing)
     */
    private function typeIdByKey(string $key): int
    {
        static $cache = [];
        if (isset($cache[$key])) return $cache[$key];

        $id = TransactionsType::query()->where('key', $key)->value('id');
        if (!$id) {
            throw ValidationException::withMessages([
                'type_key' => "TransactionsType key not found: {$key}. Please seed transaction types with keys.",
            ]);
        }

        return $cache[$key] = (int) $id;
    }

    /**
     * ✅ Phase 2 helper:
     * Active + NotDeleted dropdown data (safe for missing columns)
     */
    private function activeOnly(string $modelClass)
    {
        $m = new $modelClass();
        $table = $m->getTable();

        $q = $modelClass::query();

        if (Schema::hasColumn($table, 'isDeleted')) {
            $q->where('isDeleted', false);
        }

        if (Schema::hasColumn($table, 'isActived')) {
            $q->where('isActived', true);
        }

        return $q;
    }

    public function index(Request $request)
    {
        $q = Transactions::query()
            ->with(['student', 'donor', 'lender', 'account', 'type']);

        // ✅ default hide deleted transactions if flags exist
        $txTable = (new Transactions())->getTable();
        if (Schema::hasColumn($txTable, 'isDeleted')) {
            $q->where('isDeleted', false);
        }
        if (Schema::hasColumn($txTable, 'isActived')) {
            $q->where('isActived', true);
        }

        // Date range
        if ($request->filled('from')) {
            $q->whereDate('transactions_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $q->whereDate('transactions_date', '<=', $request->to);
        }

        // Account
        if ($request->filled('account_id')) {
            $q->where('account_id', $request->account_id);
        }

        // Type
        if ($request->filled('type_id')) {
            $q->where('transactions_type_id', $request->type_id);
        }

        // Search
        if ($request->filled('search')) {
            $s = trim($request->search);

            $q->where(function ($qq) use ($s) {
                $qq->where('recipt_no', 'like', "%{$s}%")
                    ->orWhere('student_book_number', 'like', "%{$s}%")
                    ->orWhere('note', 'like', "%{$s}%")
                    ->orWhere('c_s_1', 'like', "%{$s}%"); // title

                if (ctype_digit($s)) {
                    $qq->orWhere('id', (int) $s)
                        ->orWhere('student_id', (int) $s)
                        ->orWhere('doner_id', (int) $s)
                        ->orWhere('lender_id', (int) $s);
                }
            });
        }

        $transactions = $q->orderByDesc('transactions_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        // ✅ dropdown data (active-only where possible)
        $accounts = $this->activeOnly(Account::class)->orderBy('name')->get();
        $types    = $this->activeOnly(TransactionsType::class)->orderBy('name')->get();

        $students = $this->activeOnly(Student::class)
            ->with(['class', 'section', 'academicYear', 'feesType'])
            ->orderBy('id', 'desc')
            ->get();

        $donors   = $this->activeOnly(Donor::class)->orderBy('id', 'desc')->get();
        $lenders  = $this->activeOnly(Lender::class)->orderBy('id', 'desc')->get();

        // Bulk dropdown data
        $years     = $this->activeOnly(AddAcademy::class)->orderBy('id', 'desc')->get();
        $months    = $this->activeOnly(AddMonth::class)->orderBy('id', 'asc')->get();
        $classes   = $this->activeOnly(AddClass::class)->orderBy('id', 'asc')->get();
        $sections  = $this->activeOnly(AddSection::class)->orderBy('id', 'asc')->get();
        $feesTypes = $this->activeOnly(AddFessType::class)->orderBy('id', 'asc')->get();

        $classDefaultFeesEndpoint = Route::has('ajax.class_default_fees')
            ? route('ajax.class_default_fees')
            : url('/ajax/class-default-fees');

        return view('transactions.center', compact(
            'transactions',
            'accounts',
            'types',
            'students',
            'donors',
            'lenders',
            'years',
            'months',
            'classes',
            'sections',
            'feesTypes',
            'classDefaultFeesEndpoint'
        ));
    }

    // =========================
    // ✅ Quick Student Create (NEW)
    // =========================
    /**
     * Transaction Center থেকে quick student create করলে
     * JSON return করবে: {ok:true, student:{id,full_name,...}}
     *
     * Route example:
     * Route::post('/transaction-center/students/quick', [TransactionCenterController::class,'quickStudentStore'])
     *   ->name('transaction-center.students.quick');
     */
    public function quickStudentStore(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'dob' => ['nullable', 'date'],
            'age' => ['nullable', 'string', 'max:50'],

            'academic_year_id' => ['required', 'exists:add_academies,id'],
            'class_id' => ['required', 'exists:add_classes,id'],
            'section_id' => ['required', 'exists:add_sections,id'],
            'fees_type_id' => ['nullable', 'exists:add_fess_types,id'],

            'roll' => [
                'required', 'integer', 'min:1',
                Rule::unique('students', 'roll')->where(function ($q) use ($request) {
                    return $q->where('academic_year_id', $request->academic_year_id)
                        ->where('class_id', $request->class_id)
                        ->where('section_id', $request->section_id)
                        ->where('isDeleted', false);
                }),
            ],

            // boarding optional (quick form এ থাকলে)
            'is_boarding' => ['nullable', 'boolean'],
            'boarding_start_date' => ['nullable', 'date'],
            'boarding_end_date' => ['nullable', 'date', 'after_or_equal:boarding_start_date'],
            'boarding_note' => ['nullable', 'string'],

            'isActived' => ['nullable', 'boolean'],
        ]);

        $validated['is_boarding'] = $request->boolean('is_boarding');
        if (!$validated['is_boarding']) {
            $validated['boarding_start_date'] = null;
            $validated['boarding_end_date'] = null;
            $validated['boarding_note'] = null;
        }

        $validated['isActived'] = $request->boolean('isActived', true);
        $validated['isDeleted'] = false;

        $student = DB::transaction(function () use ($validated) {
            return Student::create($validated);
        });

        // ✅ dropdown refresh friendly response
        return response()->json([
            'ok' => true,
            'student' => [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'roll' => $student->roll,
                'mobile' => $student->mobile,
                'academic_year_id' => $student->academic_year_id,
                'class_id' => $student->class_id,
                'section_id' => $student->section_id,
                'fees_type_id' => $student->fees_type_id,
            ],
        ]);
    }

    // =========================
    // Helpers
    // =========================

    private function ensureSingleParty(array $data): void
    {
        $count = 0;
        foreach (['student_id', 'doner_id', 'lender_id'] as $k) {
            if (!empty($data[$k])) $count++;
        }

        if ($count > 1) {
            throw ValidationException::withMessages([
                'party' => 'এক transaction এ শুধু ১টা party হবে: student_id OR doner_id OR lender_id (একসাথে একাধিক নয়)।',
            ]);
        }
    }

    private function calcTotalFees(array $data): float
    {
        $monthly    = (float)($data['monthly_fees'] ?? 0);
        $boarding   = (float)($data['boarding_fees'] ?? 0);
        $management = (float)($data['management_fees'] ?? 0);
        $exam       = (float)($data['exam_fees'] ?? 0);
        $others     = (float)($data['others_fees'] ?? 0);

        return $monthly + $boarding + $management + $exam + $others;
    }

    private function defaultNoteFor(string $typeKey, array $validated): string
    {
        return match ($typeKey) {
            'student_fee'    => 'Student Fee',
            'donation'       => 'Donation',
            'loan_taken'     => 'Loan Taken',
            'loan_repayment' => 'Loan Repayment',
            'expense'        => $validated['title'] ?? 'Expense',
            'income'         => $validated['title'] ?? 'Income',
            default          => 'Transaction',
        };
    }

    // =========================
    // Phase 2: AJAX Class Default Fees
    // =========================
    public function classDefaultFees(Request $request)
    {
        $validated = $request->validate([
            'class_id'     => 'required|integer|exists:add_classes,id',
            'fees_type_id' => 'nullable|integer',
            'fess_type_id' => 'nullable|integer',
        ]);

        $classId = (int) $validated['class_id'];

        $q = AddRegistrationFess::query()->where('class_id', $classId);

        $table = (new AddRegistrationFess())->getTable();
        if (Schema::hasColumn($table, 'isDeleted')) $q->where('isDeleted', false);
        if (Schema::hasColumn($table, 'isActived')) $q->where('isActived', true);

        $row = $q->orderByDesc('id')->first();

        if (!$row) {
            return response()->json(['found' => false, 'class_id' => $classId]);
        }

        $attrs = $row->getAttributes();
        $get = function (array $cands) use ($row, $attrs) {
            foreach ($cands as $col) {
                if (array_key_exists($col, $attrs)) {
                    return (float) ($row->getAttribute($col) ?? 0);
                }
            }
            return 0.0;
        };

        $monthly    = $get(['monthly_fee', 'monthly_fees', 'monthly']);
        $boarding   = $get(['boarding_fee', 'boarding_fees', 'boarding']);
        $management = $get(['management_fee', 'management_fees', 'management']);
        $exam       = $get(['examination_fee', 'exam_fees', 'exam_fee', 'exam']);
        $others     = $get(['other', 'others_fees', 'others_fee', 'others']);

        $total = $monthly + $boarding + $management + $exam + $others;

        return response()->json([
            'found' => true,
            'source_id' => $row->id,
            'class_id' => $classId,
            'monthly_fees' => $monthly,
            'boarding_fees' => $boarding,
            'management_fees' => $management,
            'exam_fees' => $exam,
            'others_fees' => $others,
            'total' => $total,
        ]);
    }

    // =========================
    // Phase 1: Quick Store
    // =========================

    public function storeQuick(Request $request)
    {
        $typeKey = (string) $request->input('type_key');

        if (!$typeKey || !in_array($typeKey, self::ALLOWED_TYPE_KEYS, true)) {
            return back()->with('error', 'Invalid type_key.');
        }

        // ✅ strict type mapping (no hardcode)
        $typeId = $this->typeIdByKey($typeKey);

        $baseRules = [
            'type_key'          => 'required|string|in:student_fee,donation,income,expense,loan_taken,loan_repayment',
            'account_id'        => 'required|exists:accounts,id',
            'transactions_date' => 'nullable|date',
            'note'              => 'nullable|string|max:500',
            'recipt_no'         => 'nullable|string|max:255',
        ];

        $typeRules = [];

        if ($typeKey === 'student_fee') {
            $typeRules = [
                'student_id' => 'required|exists:students,id',

                'months_id'        => 'required|exists:add_months,id',
                'academic_year_id' => 'nullable|exists:add_academies,id',
                'class_id'         => 'nullable|exists:add_classes,id',
                'section_id'       => 'nullable|exists:add_sections,id',
                'fess_type_id'     => 'nullable|exists:add_fess_types,id',

                'admission_fee'   => 'nullable|numeric|min:0',
                'monthly_fees'    => 'nullable|numeric|min:0',
                'boarding_fees'   => 'nullable|numeric|min:0',
                'management_fees' => 'nullable|numeric|min:0',
                'exam_fees'       => 'nullable|numeric|min:0',
                'others_fees'     => 'nullable|numeric|min:0',
                'total_fees'      => 'nullable|numeric|min:0',

                'student_book_number' => 'nullable|string|max:255',
            ];
        } elseif ($typeKey === 'donation') {
            $typeRules = [
                'doner_id' => 'required|exists:donors,id',
                'amount'   => 'required|numeric|min:0.01',
                'title'    => 'nullable|string|max:255',
            ];
        } elseif ($typeKey === 'loan_taken' || $typeKey === 'loan_repayment') {
            $typeRules = [
                'lender_id' => 'required|exists:lenders,id',
                'amount'    => 'required|numeric|min:0.01',
                'title'     => 'nullable|string|max:255',
            ];
        } elseif ($typeKey === 'expense') {
            $typeRules = [
                'title'       => 'required|string|max:255',
                'amount'      => 'required|numeric|min:0.01',
                'catagory_id' => 'nullable|exists:catagories,id',
                'expens_id'   => 'nullable|exists:expens,id',
            ];
        } elseif ($typeKey === 'income') {
            $typeRules = [
                'title'       => 'required|string|max:255',
                'amount'      => 'required|numeric|min:0.01',
                'catagory_id' => 'nullable|exists:catagories,id',
                'income_id'   => 'nullable|exists:incomes,id',
            ];
        }

        $validated = $request->validate(array_merge($baseRules, $typeRules));
        $this->ensureSingleParty($validated);

        $txDate = $validated['transactions_date'] ?? Carbon::now()->toDateString();

        $txData = [
            'transactions_type_id' => $typeId,
            'account_id'           => $validated['account_id'],
            'transactions_date'    => $txDate,
            'created_by_id'        => auth()->id(),
            'isActived'            => true,
            'isDeleted'            => false,

            'note'      => $validated['note'] ?? $this->defaultNoteFor($typeKey, $validated),
            'recipt_no' => $validated['recipt_no'] ?? null,

            'student_id' => $validated['student_id'] ?? null,
            'doner_id'   => $validated['doner_id'] ?? null,
            'lender_id'  => $validated['lender_id'] ?? null,
        ];

        if ($typeKey === 'student_fee') {
            $student = Student::query()
                ->select('id', 'roll', 'class_id', 'section_id', 'academic_year_id', 'fees_type_id')
                ->find($validated['student_id']);

            $txData['months_id']        = $validated['months_id'];
            $txData['academic_year_id'] = $validated['academic_year_id'] ?? ($student?->academic_year_id);
            $txData['class_id']         = $validated['class_id'] ?? ($student?->class_id);
            $txData['section_id']       = $validated['section_id'] ?? ($student?->section_id);
            $txData['fess_type_id']     = $validated['fess_type_id'] ?? ($student?->fees_type_id);

            $txData['c_i_1'] = $student?->roll;
            $txData['c_d_1'] = $validated['admission_fee'] ?? null;
            $txData['student_book_number'] = $validated['student_book_number'] ?? null;

            $txData['monthly_fees']    = $validated['monthly_fees'] ?? null;
            $txData['boarding_fees']   = $validated['boarding_fees'] ?? null;
            $txData['management_fees'] = $validated['management_fees'] ?? null;
            $txData['exam_fees']       = $validated['exam_fees'] ?? null;
            $txData['others_fees']     = $validated['others_fees'] ?? null;

            $admission = (float)($validated['admission_fee'] ?? 0);
            $sum       = $admission + $this->calcTotalFees($validated);

            $givenTotal = isset($validated['total_fees']) ? (float)$validated['total_fees'] : 0;
            $total      = $givenTotal > 0 ? $givenTotal : $sum;

            if ($total <= 0) {
                throw ValidationException::withMessages([
                    'total_fees' => 'Student fee amount must be greater than 0.',
                ]);
            }

            $txData['total_fees'] = $total;

            $split = TransactionLedger::split('student_fee', $total);
            $txData['debit']  = $split['debit'];
            $txData['credit'] = $split['credit'];

            $txData['doner_id']  = null;
            $txData['lender_id'] = null;
        }

        if ($typeKey === 'donation') {
            $amount = (float)$validated['amount'];
            $split = TransactionLedger::split('donation', $amount);
            $txData['debit']  = $split['debit'];
            $txData['credit'] = $split['credit'];
            $txData['c_s_1'] = $validated['title'] ?? 'Donation';
            $txData['total_fees'] = null;
            $txData['student_id'] = null;
            $txData['lender_id']  = null;
        }

        if ($typeKey === 'loan_taken') {
            $amount = (float)$validated['amount'];
            $split = TransactionLedger::split('loan_taken', $amount);
            $txData['debit']  = $split['debit'];
            $txData['credit'] = $split['credit'];
            $txData['c_s_1'] = $validated['title'] ?? 'Loan Taken';
            $txData['total_fees'] = null;
            $txData['student_id'] = null;
            $txData['doner_id']   = null;
        }

        if ($typeKey === 'loan_repayment') {
            $amount = (float)$validated['amount'];
            $split = TransactionLedger::split('loan_repayment', $amount);
            $txData['debit']  = $split['debit'];
            $txData['credit'] = $split['credit'];
            $txData['c_s_1'] = $validated['title'] ?? 'Loan Repayment';
            $txData['total_fees'] = null;
            $txData['student_id'] = null;
            $txData['doner_id']   = null;
        }

        if ($typeKey === 'expense') {
            $amount = (float)$validated['amount'];
            $txData['c_s_1'] = $validated['title'];
            $txData['catagory_id'] = $validated['catagory_id'] ?? null;
            $txData['expens_id']   = $validated['expens_id'] ?? null;

            $split = TransactionLedger::split('expense', $amount);
            $txData['debit']  = $split['debit'];
            $txData['credit'] = $split['credit'];

            $txData['total_fees'] = null;
            $txData['student_id'] = null;
            $txData['doner_id']   = null;
            $txData['lender_id']  = null;
        }

        if ($typeKey === 'income') {
            $amount = (float)$validated['amount'];
            $txData['c_s_1'] = $validated['title'];
            $txData['catagory_id'] = $validated['catagory_id'] ?? null;
            $txData['income_id']   = $validated['income_id'] ?? null;

            $split = TransactionLedger::split('income', $amount);
            $txData['debit']  = $split['debit'];
            $txData['credit'] = $split['credit'];

            $txData['total_fees'] = null;
            $txData['student_id'] = null;
            $txData['doner_id']   = null;
            $txData['lender_id']  = null;
        }

        DB::transaction(function () use ($txData) {
            Transactions::create($txData);
        });

        return redirect()->to('/transaction-center')->with('success', 'Transaction saved successfully!');
    }

    public function receipt(Transactions $transaction)
    {
        $transaction->load(['student', 'donor', 'lender', 'account', 'type']);
        return view('transactions.receipt', compact('transaction'));
    }

    public function edit(Transactions $transaction)
    {
        $transaction->load(['student', 'donor', 'lender', 'account', 'type']);

        $accounts = $this->activeOnly(Account::class)->orderBy('name')->get();
        $types    = $this->activeOnly(TransactionsType::class)->orderBy('name')->get();

        $students = $this->activeOnly(Student::class)
            ->with(['class', 'section', 'academicYear', 'feesType'])
            ->orderBy('id', 'desc')
            ->get();

        $donors  = $this->activeOnly(Donor::class)->orderBy('id', 'desc')->get();
        $lenders = $this->activeOnly(Lender::class)->orderBy('id', 'desc')->get();

        $years     = $this->activeOnly(AddAcademy::class)->orderBy('id', 'desc')->get();
        $months    = $this->activeOnly(AddMonth::class)->orderBy('id', 'asc')->get();
        $classes   = $this->activeOnly(AddClass::class)->orderBy('id', 'asc')->get();
        $sections  = $this->activeOnly(AddSection::class)->orderBy('id', 'asc')->get();
        $feesTypes = $this->activeOnly(AddFessType::class)->orderBy('id', 'asc')->get();

        return view('transactions.edit', compact(
            'transaction',
            'accounts',
            'types',
            'students',
            'donors',
            'lenders',
            'years',
            'months',
            'classes',
            'sections',
            'feesTypes'
        ));
    }

    public function update(Request $request, Transactions $transaction)
    {
        $transaction->load(['type']);

        $typeKey = data_get($transaction, 'type.key');

        if (!$typeKey) {
            $name = strtolower((string) data_get($transaction, 'type.name', ''));
            if (str_contains($name, 'student') && str_contains($name, 'fee')) $typeKey = 'student_fee';
            elseif (str_contains($name, 'don')) $typeKey = 'donation';
            elseif (str_contains($name, 'repay')) $typeKey = 'loan_repayment';
            elseif (str_contains($name, 'loan')) $typeKey = 'loan_taken';
            elseif (str_contains($name, 'expense')) $typeKey = 'expense';
            elseif (str_contains($name, 'income')) $typeKey = 'income';
        }

        if (!$typeKey || !in_array($typeKey, self::ALLOWED_TYPE_KEYS, true)) {
            return back()->with('error', 'Type key not found for this transaction.');
        }

        $typeId = $this->typeIdByKey($typeKey);

        $baseRules = [
            'account_id'        => 'required|exists:accounts,id',
            'transactions_date' => 'nullable|date',
            'note'              => 'nullable|string|max:500',
            'recipt_no'         => 'nullable|string|max:255',
        ];

        $typeRules = [];

        if ($typeKey === 'student_fee') {
            $typeRules = [
                'student_id' => 'required|exists:students,id',
                'months_id'  => 'required|exists:add_months,id',

                'academic_year_id' => 'nullable|exists:add_academies,id',
                'class_id'         => 'nullable|exists:add_classes,id',
                'section_id'       => 'nullable|exists:add_sections,id',
                'fess_type_id'     => 'nullable|exists:add_fess_types,id',

                'admission_fee'   => 'nullable|numeric|min:0',
                'monthly_fees'    => 'nullable|numeric|min:0',
                'boarding_fees'   => 'nullable|numeric|min:0',
                'management_fees' => 'nullable|numeric|min:0',
                'exam_fees'       => 'nullable|numeric|min:0',
                'others_fees'     => 'nullable|numeric|min:0',
                'total_fees'      => 'nullable|numeric|min:0',

                'student_book_number' => 'nullable|string|max:255',
            ];
        } elseif ($typeKey === 'donation') {
            $typeRules = [
                'doner_id' => 'required|exists:donors,id',
                'amount'   => 'required|numeric|min:0.01',
                'title'    => 'nullable|string|max:255',
            ];
        } elseif ($typeKey === 'loan_taken' || $typeKey === 'loan_repayment') {
            $typeRules = [
                'lender_id' => 'required|exists:lenders,id',
                'amount'    => 'required|numeric|min:0.01',
                'title'     => 'nullable|string|max:255',
            ];
        } elseif ($typeKey === 'expense') {
            $typeRules = [
                'title'       => 'required|string|max:255',
                'amount'      => 'required|numeric|min:0.01',
                'catagory_id' => 'nullable|exists:catagories,id',
                'expens_id'   => 'nullable|exists:expens,id',
            ];
        } elseif ($typeKey === 'income') {
            $typeRules = [
                'title'       => 'required|string|max:255',
                'amount'      => 'required|numeric|min:0.01',
                'catagory_id' => 'nullable|exists:catagories,id',
                'income_id'   => 'nullable|exists:incomes,id',
            ];
        }

        $validated = $request->validate(array_merge($baseRules, $typeRules));
        $this->ensureSingleParty($validated);

        $txDate = $validated['transactions_date'] ?? Carbon::now()->toDateString();

        $txData = [
            'transactions_type_id' => $typeId,
            'account_id'           => $validated['account_id'],
            'transactions_date'    => $txDate,
            'note'                 => $validated['note'] ?? $this->defaultNoteFor($typeKey, $validated),
            'recipt_no'            => $validated['recipt_no'] ?? null,

            'student_id' => null,
            'doner_id'   => null,
            'lender_id'  => null,
        ];

        if ($typeKey === 'student_fee') {
            $student = Student::query()
                ->select('id', 'roll', 'class_id', 'section_id', 'academic_year_id', 'fees_type_id')
                ->find($validated['student_id']);

            $txData['student_id']        = $validated['student_id'];
            $txData['months_id']         = $validated['months_id'];
            $txData['academic_year_id']  = $validated['academic_year_id'] ?? ($student?->academic_year_id);
            $txData['class_id']          = $validated['class_id'] ?? ($student?->class_id);
            $txData['section_id']        = $validated['section_id'] ?? ($student?->section_id);
            $txData['fess_type_id']      = $validated['fess_type_id'] ?? ($student?->fees_type_id);

            $txData['c_i_1']             = $student?->roll;
            $txData['c_d_1']             = $validated['admission_fee'] ?? null;

            $txData['student_book_number'] = $validated['student_book_number'] ?? null;

            $txData['monthly_fees']      = $validated['monthly_fees'] ?? null;
            $txData['boarding_fees']     = $validated['boarding_fees'] ?? null;
            $txData['management_fees']   = $validated['management_fees'] ?? null;
            $txData['exam_fees']         = $validated['exam_fees'] ?? null;
            $txData['others_fees']       = $validated['others_fees'] ?? null;

            $admission = (float)($validated['admission_fee'] ?? 0);
            $sum = $admission + $this->calcTotalFees($validated);

            $givenTotal = isset($validated['total_fees']) ? (float)$validated['total_fees'] : 0;
            $total = $givenTotal > 0 ? $givenTotal : $sum;

            if ($total <= 0) {
                throw ValidationException::withMessages([
                    'total_fees' => 'Student fee amount must be greater than 0.',
                ]);
            }

            $txData['total_fees'] = $total;

            $split = TransactionLedger::split('student_fee', $total);
            $txData['debit']  = $split['debit'];
            $txData['credit'] = $split['credit'];
        }

        if ($typeKey === 'donation') {
            $amount = (float)$validated['amount'];
            $txData['doner_id'] = $validated['doner_id'];
            $txData['total_fees'] = null;
            $txData['c_s_1'] = $validated['title'] ?? 'Donation';

            $split = TransactionLedger::split('donation', $amount);
            $txData['debit']  = $split['debit'];
            $txData['credit'] = $split['credit'];
        }

        if ($typeKey === 'loan_taken') {
            $amount = (float)$validated['amount'];
            $txData['lender_id'] = $validated['lender_id'];
            $txData['total_fees'] = null;
            $txData['c_s_1'] = $validated['title'] ?? 'Loan Taken';

            $split = TransactionLedger::split('loan_taken', $amount);
            $txData['debit']  = $split['debit'];
            $txData['credit'] = $split['credit'];
        }

        if ($typeKey === 'loan_repayment') {
            $amount = (float)$validated['amount'];
            $txData['lender_id'] = $validated['lender_id'];
            $txData['total_fees'] = null;
            $txData['c_s_1'] = $validated['title'] ?? 'Loan Repayment';

            $split = TransactionLedger::split('loan_repayment', $amount);
            $txData['debit']  = $split['debit'];
            $txData['credit'] = $split['credit'];
        }

        if ($typeKey === 'expense') {
            $amount = (float)$validated['amount'];
            $txData['c_s_1'] = $validated['title'];
            $txData['total_fees'] = null;
            $txData['catagory_id'] = $validated['catagory_id'] ?? null;
            $txData['expens_id']   = $validated['expens_id'] ?? null;

            $split = TransactionLedger::split('expense', $amount);
            $txData['debit']  = $split['debit'];
            $txData['credit'] = $split['credit'];
        }

        if ($typeKey === 'income') {
            $amount = (float)$validated['amount'];
            $txData['c_s_1'] = $validated['title'];
            $txData['total_fees'] = null;
            $txData['catagory_id'] = $validated['catagory_id'] ?? null;
            $txData['income_id']   = $validated['income_id'] ?? null;

            $split = TransactionLedger::split('income', $amount);
            $txData['debit']  = $split['debit'];
            $txData['credit'] = $split['credit'];
        }

        DB::transaction(function () use ($transaction, $txData) {
            $transaction->update($txData);
        });

        return redirect()->to('/transaction-center')->with('success', 'Transaction updated successfully!');
    }

    public function destroy(Transactions $transaction)
    {
        DB::transaction(function () use ($transaction) {

            if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($transaction))) {
                $transaction->delete();
                return;
            }

            $table = $transaction->getTable();

            if (Schema::hasColumn($table, 'isDeleted')) {
                $transaction->isDeleted = true;
            }

            if (Schema::hasColumn($table, 'isActived')) {
                $transaction->isActived = false;
            }

            if (!Schema::hasColumn($table, 'isDeleted') && !Schema::hasColumn($table, 'isActived')) {
                $transaction->delete();
                return;
            }

            $transaction->save();
        });

        return redirect()->to('/transaction-center')->with('success', 'Transaction deleted successfully!');
    }
}