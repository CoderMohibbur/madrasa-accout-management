<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AddAcademy;
use App\Models\AddClass;
use App\Models\AddFessType;
use App\Models\AddMonth;
use App\Models\AddSection;
use App\Models\Student;
use App\Models\Transactions;
use App\Models\TransactionsType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * ✅ Phase-1/2 rule: NEVER hardcode transactions_type_id
     * Always key -> id mapping
     */
    private function typeIdByKey(string $key): int
    {
        static $cache = [];

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $id = TransactionsType::query()->where('key', $key)->value('id');

        if (!$id) {
            abort(500, "TransactionsType key not found: {$key}. Please seed transaction types with keys.");
        }

        return $cache[$key] = (int) $id;
    }

    /**
     * ✅ Phase 3: Student List + Filters
     * Filters: academic_year_id, class_id, section_id, is_boarding(0/1), search
     */
    public function index(Request $request)
    {
        /**
         * ✅ Phase 3: Student List + Filters + Live Search (AJAX)
         * Filters: academic_year_id, class_id, section_id, is_boarding(0/1), search
         */
        $request->validate([
            'academic_year_id' => ['nullable', 'integer'],
            'class_id'         => ['nullable', 'integer'],
            'section_id'       => ['nullable', 'integer'],
            'is_boarding'      => ['nullable', 'in:0,1'],
            'search'           => ['nullable', 'string', 'max:100'],
            'page'             => ['nullable', 'integer'],
        ]);

        $q = Student::query()->with([
            'class',
            'section',
            'academicYear',
            'feesType',
        ]);

        // Soft/flag delete compatibility
        if (Schema::hasColumn('students', 'isDeleted')) {
            $q->where('isDeleted', false);
        }

        // --------------------------
        // Filters
        // --------------------------
        if ($request->filled('academic_year_id')) {
            $q->where('academic_year_id', (int) $request->academic_year_id);
        }

        if ($request->filled('class_id')) {
            $q->where('class_id', (int) $request->class_id);
        }

        if ($request->filled('section_id')) {
            $q->where('section_id', (int) $request->section_id);
        }

        // ✅ "0" (Non-Boarding) must work
        if ($request->has('is_boarding') && $request->is_boarding !== '') {
            $q->where('is_boarding', (int) $request->is_boarding);
        }

        // --------------------------
        // Search (schema-safe, match anything)
        // --------------------------
        $raw = trim((string) $request->input('search', ''));

        if ($raw !== '') {
            $s = addcslashes($raw, '\\%_'); // escape LIKE wildcards
            $digitsOnly = preg_replace('/\D+/', '', $raw);

            $textColumns = [];
            foreach (['full_name', 'father_name', 'mobile', 'email'] as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $textColumns[] = $col;
                }
            }
            $hasRoll = Schema::hasColumn('students', 'roll');

            $q->where(function ($qq) use ($textColumns, $hasRoll, $s, $raw, $digitsOnly) {
                $added = false;

                // full_name / father_name / mobile / email
                foreach ($textColumns as $col) {
                    if (!$added) {
                        $qq->where($col, 'like', "%{$s}%");
                        $added = true;
                    } else {
                        $qq->orWhere($col, 'like', "%{$s}%");
                    }
                }

                // roll
                if ($hasRoll) {
                    // numeric exact match (fast)
                    if (is_numeric($raw)) {
                        $qq->orWhere('roll', (int) $raw);
                    }
                    // also allow partial (string) match
                    $qq->orWhere('roll', 'like', "%{$s}%");
                    $added = true;
                }

                // digits-only fallback for mobile (017-xx / 017 xx)
                if (!empty($digitsOnly) && in_array('mobile', $textColumns, true) && $digitsOnly !== $raw) {
                    $qq->orWhere('mobile', 'like', "%{$digitsOnly}%");
                    $added = true;
                }

                // If no searchable columns exist, prevent accidental full scan match
                if (!$added) {
                    $qq->whereRaw('1 = 0');
                }
            });
        }

        // Pagination
        $students = $q->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        // ✅ AJAX Live Search: only return the results block (fast)
        if ($request->ajax()) {
            return view('students.partials.results', compact('students'));
        }

        // Dropdowns (only for full page load)
        $classes = AddClass::query()
            ->when(Schema::hasColumn('add_classes', 'isDeleted') ?? false, fn($qq) => $qq->where('isDeleted', false))
            ->orderBy('name')
            ->get();

        $sections = AddSection::query()
            ->when(Schema::hasColumn('add_sections', 'isDeleted') ?? false, fn($qq) => $qq->where('isDeleted', false))
            ->orderBy('name')
            ->get();

        $fees_types = AddFessType::query()
            ->orderBy('name')
            ->get();

        $academic_years = AddAcademy::query()
            ->orderByDesc('id')
            ->get();

        return view('students.index', compact(
            'students',
            'classes',
            'sections',
            'fees_types',
            'academic_years'
        ));
    }

    /**
     * ✅ Admission Form
     */
    public function create()
    {
        $classes = AddClass::query()->where('isDeleted', false)->orderBy('name')->get();
        $sections = AddSection::query()->where('isDeleted', false)->orderBy('name')->get();
        $fees_types = AddFessType::query()->where('isDeleted', false)->orderBy('name')->get();
        $academic_years = AddAcademy::query()->where('isDeleted', false)->orderByDesc('id')->get();

        return view('students.create', compact('classes', 'sections', 'fees_types', 'academic_years'));
    }

    /**
     * ✅ Store Admission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'dob' => ['nullable', 'date'],

            'class_id' => ['required', 'exists:add_classes,id'],
            'section_id' => ['required', 'exists:add_sections,id'],
            'academic_year_id' => ['required', 'exists:add_academies,id'],

            'roll' => [
                'required',
                'integer',
                'min:1',
                // ✅ prevent duplicates per year+class+section+roll
                Rule::unique('students', 'roll')->where(function ($q) use ($request) {
                    return $q->where('academic_year_id', $request->academic_year_id)
                        ->where('class_id', $request->class_id)
                        ->where('section_id', $request->section_id)
                        ->where('isDeleted', false);
                }),
            ],

            'email' => ['nullable', 'email', 'max:255'],
            'mobile' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],

            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'age' => ['nullable', 'string', 'max:50'],

            'fees_type_id' => ['nullable', 'exists:add_fess_types,id'],
            'scholarship_amount' => ['nullable', 'numeric', 'min:0'],

            // Boarding
            'is_boarding' => ['nullable', 'boolean'],
            'boarding_start_date' => ['nullable', 'date'],
            'boarding_end_date' => ['nullable', 'date', 'after_or_equal:boarding_start_date'],
            'boarding_note' => ['nullable', 'string'],

            'isActived' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

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

        return redirect()->route('students.show', $student)->with('success', 'Student admitted successfully.');
    }

    /**
     * ✅ Student Profile + Fee history + Tx history (Phase-3 Final)
     */
    public function show(Request $request, Student $student)
    {
        $student->load(['class', 'section', 'academicYear', 'feesType']);

        // ✅ Date range (default: current month)
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfMonth()->endOfDay();

        // ✅ Phase-1 rule: key -> id (no hardcode)
        $studentFeeTypeId = $this->typeIdByKey('student_fee');

        // ✅ Base query for student fee transactions
        $feeTxBase = Transactions::query()
            ->where('student_id', $student->id)
            ->where('transactions_type_id', $studentFeeTypeId);

        // ✅ Student fee = CREDIT (clean & correct)
        $feeTotalPaid = (float) (clone $feeTxBase)->sum(DB::raw('COALESCE(credit,0)'));

        $feePaidInRange = (float) (clone $feeTxBase)
            ->whereBetween('transactions_date', [$from->toDateString(), $to->toDateString()])
            ->sum(DB::raw('COALESCE(credit,0)'));

        // Due tracking not implemented yet (Advanced phase)
        $feeTotalDue = 0.0;
        $feeDueInRange = 0.0;

        // ✅ Month-wise fee history (for profile view)
        $feeHistory = (clone $feeTxBase)
            ->select([
                'months_id',
                DB::raw('MAX(transactions_date) as last_date'),
                DB::raw('SUM(COALESCE(credit,0)) as total_paid'),
            ])
            ->whereNotNull('months_id')
            ->groupBy('months_id')
            ->orderBy('months_id')
            ->with('month') // Transactions model must have month() relation
            ->get();

        // ✅ IMPORTANT: your show.blade.php expects $recentStudentTx
        // So we provide it (latest 10, no date filter)
        $recentStudentTx = Transactions::query()
            ->with(['type', 'account', 'month'])
            ->where('student_id', $student->id)
            ->orderByDesc('transactions_date')
            ->orderByDesc('id')
            ->take(10)
            ->get();

        // ✅ Full transaction history (date range + paginate)
        $txs = Transactions::query()
            ->with(['type', 'account', 'month'])
            ->where('student_id', $student->id)
            ->whereBetween('transactions_date', [$from->toDateString(), $to->toDateString()])
            ->orderByDesc('transactions_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('students.show', compact(
            'student',
            'from',
            'to',
            'feeTotalPaid',
            'feePaidInRange',
            'feeTotalDue',
            'feeDueInRange',
            'feeHistory',
            'recentStudentTx',
            'txs'
        ));
    }




    public function updateBoarding(Request $request, Student $student)
    {
        // deleted student protection (Phase 1–5 style)
        if ((bool)($student->isDeleted ?? false) === true) {
            return back()->with('error', 'Deleted student can not be updated.');
        }

        $validated = $request->validate([
            'is_boarding' => ['required', 'boolean'],
            'boarding_start_date' => ['nullable', 'date'],
            'boarding_end_date' => ['nullable', 'date', 'after_or_equal:boarding_start_date'],
            'boarding_note' => ['nullable', 'string', 'max:500'],
        ]);

        $isBoarding = $request->boolean('is_boarding');

        $data = [
            'is_boarding' => $isBoarding,
        ];

        if ($isBoarding) {
            $data['boarding_start_date'] = $validated['boarding_start_date'] ?? Carbon::now()->toDateString();
            $data['boarding_end_date']   = $validated['boarding_end_date'] ?? null;
            $data['boarding_note']       = $validated['boarding_note'] ?? null;
        } else {
            $data['boarding_start_date'] = null;
            $data['boarding_end_date']   = null;
            $data['boarding_note']       = null;
        }

        DB::transaction(function () use ($student, $data) {
            $student->update($data);
        });

        return back()->with('success', 'Boarding status updated.');
    }

    /**
     * (Optional) Edit/Update kept consistent (so old links won't break)
     */
    public function edit(Student $student)
    {
        $classes = AddClass::query()->where('isDeleted', false)->orderBy('name')->get();
        $sections = AddSection::query()->where('isDeleted', false)->orderBy('name')->get();
        $fees_types = AddFessType::query()->where('isDeleted', false)->orderBy('name')->get();
        $academic_years = AddAcademy::query()->where('isDeleted', false)->orderByDesc('id')->get();

        return view('students.create', compact('student', 'classes', 'sections', 'fees_types', 'academic_years'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'dob' => ['nullable', 'date'],

            'class_id' => ['required', 'exists:add_classes,id'],
            'section_id' => ['required', 'exists:add_sections,id'],
            'academic_year_id' => ['required', 'exists:add_academies,id'],

            'roll' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('students', 'roll')
                    ->ignore($student->id)
                    ->where(function ($q) use ($request) {
                        return $q->where('academic_year_id', $request->academic_year_id)
                            ->where('class_id', $request->class_id)
                            ->where('section_id', $request->section_id)
                            ->where('isDeleted', false);
                    }),
            ],

            'email' => ['nullable', 'email', 'max:255'],
            'mobile' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],

            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'age' => ['nullable', 'string', 'max:50'],

            'fees_type_id' => ['nullable', 'exists:add_fess_types,id'],
            'scholarship_amount' => ['nullable', 'numeric', 'min:0'],

            'is_boarding' => ['nullable', 'boolean'],
            'boarding_start_date' => ['nullable', 'date'],
            'boarding_end_date' => ['nullable', 'date', 'after_or_equal:boarding_start_date'],
            'boarding_note' => ['nullable', 'string'],

            'isActived' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $validated['is_boarding'] = $request->boolean('is_boarding');
        if (!$validated['is_boarding']) {
            $validated['boarding_start_date'] = null;
            $validated['boarding_end_date'] = null;
            $validated['boarding_note'] = null;
        }

        // ✅ normalize boolean like store()
        $validated['isActived'] = $request->boolean('isActived', (bool) $student->isActived);

        DB::transaction(function () use ($student, $validated) {
            $student->update($validated);
        });

        return redirect()->route('students.show', $student)->with('success', 'Student updated successfully.');
    }

    /**
     * ✅ Safer destroy: do not hard delete if transactions exist
     * Use isDeleted flag (so Transaction Center history doesn’t break)
     */
    public function destroy(Student $student)
    {
        $hasTx = Transactions::query()->where('student_id', $student->id)->exists();

        if ($hasTx) {
            $student->update([
                'isDeleted' => true,
                'isActived' => false,
            ]);
            return redirect()->route('students.index')->with('success', 'Student archived (has transactions).');
        }

        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    // Keep legacy method untouched (optional)
    public function Student_Fees()
    {
        $transactions = Transactions::all();
        $students = Student::all();
        $accounts = Account::all();
        $classes = AddClass::all();
        $sections = AddSection::all();
        $months = AddMonth::all();
        $years = AddAcademy::all();
        $feestypes = AddFessType::all();
        $transactionTypes = TransactionsType::all();

        return view('students.add-fees_copy', compact(
            'transactions',
            'students',
            'accounts',
            'classes',
            'sections',
            'years',
            'months',
            'feestypes',
            'transactionTypes'
        ));
    }
}
