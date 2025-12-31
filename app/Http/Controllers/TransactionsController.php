<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\Student;
use App\Models\AddClass;
use App\Models\AddMonth;
use App\Models\AddAcademy;
use App\Models\AddSection;
use App\Models\AddFessType;
use App\Models\Transactions;
use App\Models\TransactionsType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class TransactionsController extends Controller
{
    /**
     * Transaction Type Key system থাকলে Student Fee type id বের করে।
     * না থাকলে null return করে (fallback হবে request->transactions_type_id)
     */
    private function typeIdByKey(?string $key): ?int
    {
        if (!$key) return null;

        // key কলাম থাকলে কাজ করবে
        // না থাকলে query error হতে পারে—তাই try/catch নিরাপদ
        try {
            $type = TransactionsType::where('key', $key)->first();
            return $type?->id;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * fees subtotal থেকে total calc
     */
    private function calcTotalFees(array $data): float
    {
        $monthly     = (float)($data['monthly_fees'] ?? 0);
        $boarding    = (float)($data['boarding_fees'] ?? 0);
        $management  = (float)($data['management_fees'] ?? 0);
        $exam        = (float)($data['exam_fees'] ?? 0);
        $others      = (float)($data['others_fees'] ?? 0);

        return $monthly + $boarding + $management + $exam + $others;
    }

    /**
     * Data Integrity: এক transaction এ শুধু ১টা party থাকবে
     * student_id OR doner_id OR lender_id (একসাথে একাধিক না)
     * (expense/income এ তিনটাই null থাকতে পারে)
     */
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

    public function index()
    {
        // Backward compatibility: কিছু view $transactionss expect করে
        $transactionss = Transactions::with('student')->latest()->paginate(20);
        $feeTransactions = $transactionss;

        $classes  = AddClass::all();
        $sections = AddSection::all();
        $months   = AddMonth::all();
        $years    = AddAcademy::all();
        $accounts = Account::all();
        $feestypes = AddFessType::all();

        $transactionTypes = TransactionsType::all();

        // create form এ edit object না থাকলে null
        $transactions = null;

        return view('students.add-fees', [
            'transactionss'     => $transactionss,
            'feeTransactions'   => $feeTransactions,
            'classes'           => $classes,
            'sections'          => $sections,
            'months'            => $months,
            'years'             => $years,
            'accounts'          => $accounts,
            'feestypes'         => $feestypes,
            'transactionTypes'  => $transactionTypes,
            'transactions'      => $transactions, // edit হলে edit() এ ভরবে
        ]);
    }

    public function list()
    {
        // ✅ এখানে আগে $transactionss overwrite হচ্ছিল
        $transactions = Transactions::with(['student'])->latest()->paginate(50);

        $students = Student::all();
        $accounts = Account::all();
        $classes  = AddClass::all();
        $sections = AddSection::all();
        $months   = AddMonth::all();
        $years    = AddAcademy::all();
        $feesTypes = AddFessType::all();
        $transactionTypes = TransactionsType::all();
        $users = User::all();

        // ✅ View ভাঙবে না: পুরানো $transactionss নামেও transactions পাঠালাম
        return view('students.list-fees', [
            'transactionss'     => $transactions,
            'transactions'      => $transactions,
            'students'          => $students,
            'accounts'          => $accounts,
            'classes'           => $classes,
            'sections'          => $sections,
            'years'             => $years,
            'months'            => $months,
            'feesTypes'         => $feesTypes,
            'feestypes'         => $feesTypes,
            'transactionTypes'  => $transactionTypes,
            'users'             => $users,
        ]);
    }

    public function fetchStudents(Request $request)
    {
        $academicYearId = $request->input('academic_year_id');
        $classId        = $request->input('class_id');
        $sectionId      = $request->input('section_id');

        $students = Student::where('academic_year_id', $academicYearId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->get();

        return response()->json($students);
    }

    public function getStudents(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:add_academies,id'],
            'class_id'         => ['required', 'exists:add_classes,id'],
            'section_id'       => ['required', 'exists:add_sections,id'],
            'months_id'        => ['required', 'exists:add_months,id'],
        ]);

        $academicYearId = $data['academic_year_id'];
        $classId        = $data['class_id'];
        $sectionId      = $data['section_id'];
        $monthId        = $data['months_id'];

        // ✅ Students
        $students = Student::query()
            ->where('academic_year_id', $academicYearId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->get();

        // ✅ Student Fee type id (key system)
        $studentFeeTypeId = TransactionsType::query()
            ->where('key', 'student_fee')
            ->value('id');

        // fallback (যদি key না থাকে)
        $studentFeeTypeId = $studentFeeTypeId ?: 1;

        // ✅ Existing transactions ONLY for student_fee
        $tx = Transactions::query()
            ->whereIn('student_id', $students->pluck('id'))
            ->where('academic_year_id', $academicYearId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->where('months_id', $monthId)
            ->where('transactions_type_id', $studentFeeTypeId)
            ->orderByDesc('transactions_date')
            ->orderByDesc('id')
            ->get()
            ->groupBy('student_id')
            ->map->first()
            ->keyBy('student_id');

        return view('students.partials.students_list', [
            'students'     => $students,
            'transactions' => $tx,
        ]);
    }

    public function store(Request $request)
    {
        // Dynamic rules (custom fields সহ)
        $rules = [
            'student_id'            => 'required|exists:students,id',
            'doner_id'              => 'nullable|exists:donors,id',
            'lender_id'             => 'nullable|exists:lenders,id',

            'fess_type_id'          => 'nullable|exists:add_fess_types,id',
            'transactions_type_id'  => 'nullable|exists:transactions_types,id',

            'student_book_number'   => 'nullable|string|max:255',
            'recipt_no'             => 'nullable|string|max:255',

            'monthly_fees'          => 'nullable|numeric|min:0',
            'boarding_fees'         => 'nullable|numeric|min:0',
            'management_fees'       => 'nullable|numeric|min:0',
            'exam_fees'             => 'nullable|numeric|min:0',
            'others_fees'           => 'nullable|numeric|min:0',
            'total_fees'            => 'nullable|numeric|min:0',

            'debit'                 => 'nullable|numeric|min:0',
            'credit'                => 'nullable|numeric|min:0',

            'transactions_date'     => 'nullable|date',
            'account_id'            => 'required|exists:accounts,id',

            'class_id'              => 'nullable|exists:add_classes,id',
            'section_id'            => 'nullable|exists:add_sections,id',
            'months_id'             => 'nullable|exists:add_months,id',
            'academic_year_id'      => 'nullable|exists:add_academies,id',

            'created_by_id'         => 'nullable|exists:users,id',
            'note'                  => 'nullable|string|max:500',

            'isActived'             => 'nullable|boolean',
        ];

        // custom c_d_1..7
        for ($i = 1; $i <= 7; $i++) {
            $rules["c_d_$i"] = 'nullable|numeric|min:0';
        }
        // custom c_s_1..8
        for ($i = 1; $i <= 8; $i++) {
            $rules["c_s_$i"] = 'nullable|string|max:255';
        }
        // custom c_i_1..6
        for ($i = 1; $i <= 6; $i++) {
            $rules["c_i_$i"] = 'nullable|integer|min:0';
        }

        $validated = $request->validate($rules);

        // ✅ integrity
        $this->ensureSingleParty($validated);

        // ✅ Student Fee flow হলে donor/lender null রাখি (আপনার plan অনুযায়ী)
        $validated['doner_id']  = null;
        $validated['lender_id'] = null;

        // ✅ type key system (student_fee) prefer
        $studentFeeTypeId = $this->typeIdByKey('student_fee');
        $validated['transactions_type_id'] = $studentFeeTypeId ?? ($validated['transactions_type_id'] ?? null);

        if (!$validated['transactions_type_id']) {
            throw ValidationException::withMessages([
                'transactions_type_id' => 'transactions type set করা নেই। transactions_types টেবিলে student_fee টাইপ/কি সেট করুন অথবা form থেকে transactions_type_id পাঠান।',
            ]);
        }

        // ✅ total calc
        if (!isset($validated['total_fees'])) {
            $validated['total_fees'] = $this->calcTotalFees($validated);
        }

        // ✅ ledger rule (student fee = cash in)
        $validated['debit']  = (float)$validated['total_fees'];
        $validated['credit'] = 0;

        // ✅ date default today
        $validated['transactions_date'] = $validated['transactions_date'] ?? Carbon::today()->toDateString();

        // ✅ created_by default auth user
        $validated['created_by_id'] = $validated['created_by_id'] ?? auth()->id();

        $validated['isActived'] = $validated['isActived'] ?? true;

        Transactions::create($validated);

        // ✅ FIX redirect
        return redirect()->route('add_student_fees.index')->with('success', 'Fees added successfully!');
    }

    public function bulkStore(Request $request)
    {
        $data = $request->validate([
            'academic_year_id'      => ['required', 'exists:add_academies,id'],
            'class_id'              => ['required', 'exists:add_classes,id'],
            'section_id'            => ['required', 'exists:add_sections,id'],
            'months_id'             => ['required', 'exists:add_months,id'],
            'account_id'            => ['required', 'exists:accounts,id'],
            'transactions_date'     => ['nullable', 'date'],

            'student_ids'           => ['required', 'array'],
            'student_ids.*'         => ['required', 'exists:students,id'],

            'student_book_number'   => ['nullable', 'array'],
            'student_book_number.*' => ['nullable', 'string', 'max:255'],

            'recipt_no'             => ['nullable', 'array'],
            'recipt_no.*' => ['nullable', 'string', 'max:255'],

            'monthly_fees'          => ['nullable', 'array'],
            'monthly_fees.*'        => ['nullable', 'numeric'],
            'boarding_fees'         => ['nullable', 'array'],
            'boarding_fees.*'       => ['nullable', 'numeric'],
            'management_fees'       => ['nullable', 'array'],
            'management_fees.*'     => ['nullable', 'numeric'],
            'exam_fees'             => ['nullable', 'array'],
            'exam_fees.*'           => ['nullable', 'numeric'],
            'others_fees'           => ['nullable', 'array'],
            'others_fees.*'         => ['nullable', 'numeric'],
            'total_fees'            => ['nullable', 'array'],
            'total_fees.*'          => ['nullable', 'numeric'],

            'debit'                 => ['nullable', 'array'],
            'debit.*'               => ['nullable', 'numeric'],
            'credit'                => ['nullable', 'array'],
            'credit.*'              => ['nullable', 'numeric'],

            'note'                  => ['nullable', 'array'],
            'note.*'                => ['nullable', 'string', 'max:500'],
        ]);

        $studentFeeTypeId = TransactionsType::query()
            ->where('key', 'student_fee')
            ->value('id');

        $studentFeeTypeId = $studentFeeTypeId ?: 1;

        $txDate = $data['transactions_date'] ?? now()->toDateString();
        $userId = auth()->id();

        foreach ($data['student_ids'] as $i => $studentId) {

            $monthly     = (float)($request->monthly_fees[$i] ?? 0);
            $boarding    = (float)($request->boarding_fees[$i] ?? 0);
            $management  = (float)($request->management_fees[$i] ?? 0);
            $exam        = (float)($request->exam_fees[$i] ?? 0);
            $others      = (float)($request->others_fees[$i] ?? 0);

            $computedTotal = $monthly + $boarding + $management + $exam + $others;
            $total = $request->total_fees[$i] ?? $computedTotal;
            $total = (float)$total;

            // ✅ Optional: empty row skip (যদি সব 0 হয়)
            $hasAny = ($total > 0) || !empty($request->note[$i] ?? null) || !empty($request->recipt_no[$i] ?? null);
            if (!$hasAny) {
                continue;
            }

            // ✅ Default debit/credit (incoming money)
            $debit  = $request->debit[$i]  ?? $total;
            $credit = $request->credit[$i] ?? 0;

            Transactions::updateOrCreate(
                [
                    'student_id'           => $studentId,
                    'transactions_type_id' => $studentFeeTypeId,
                    'academic_year_id'     => $data['academic_year_id'],
                    'class_id'             => $data['class_id'],
                    'section_id'           => $data['section_id'],
                    'months_id'            => $data['months_id'],
                ],
                [
                    // ✅ Integrity: only 1 party
                    'doner_id'            => null,
                    'lender_id'           => null,

                    'student_book_number' => $request->student_book_number[$i] ?? null,
                    'recipt_no'           => $request->recipt_no[$i] ?? null,

                    'monthly_fees'        => $monthly,
                    'boarding_fees'       => $boarding,
                    'management_fees'     => $management,
                    'exam_fees'           => $exam,
                    'others_fees'         => $others,
                    'total_fees'          => $total,

                    'debit'               => $debit,
                    'credit'              => $credit,

                    'transactions_date'   => $txDate,
                    'account_id'          => $data['account_id'],
                    'created_by_id'       => $userId,

                    'note'                => $request->note[$i] ?? null,
                    'isActived'           => 1,
                    'isDeleted'           => 0,
                ]
            );
        }

        return back()->with('success', 'Student fees saved successfully!');
    }


    public function edit($id)
    {
        $transactions = Transactions::findOrFail($id);

        // index() এর সব data আবার পাঠাই যাতে add-fees view ভাঙে না
        $transactionss = Transactions::with('student')->latest()->paginate(20);
        $feeTransactions = $transactionss;

        $classes  = AddClass::all();
        $sections = AddSection::all();
        $months   = AddMonth::all();
        $years    = AddAcademy::all();
        $accounts = Account::all();
        $feestypes = AddFessType::all();
        $transactionTypes = TransactionsType::all();

        return view('students.add-fees', [
            'transactions'      => $transactions,
            'transactionss'     => $transactionss,
            'feeTransactions'   => $feeTransactions,
            'classes'           => $classes,
            'sections'          => $sections,
            'months'            => $months,
            'years'             => $years,
            'accounts'          => $accounts,
            'feestypes'         => $feestypes,
            'transactionTypes'  => $transactionTypes,
        ]);
    }

    public function update(Request $request, $id)
    {
        $tx = Transactions::findOrFail($id);

        $rules = [
            'student_id'            => 'nullable|exists:students,id',
            'doner_id'              => 'nullable|exists:donors,id',
            'lender_id'             => 'nullable|exists:lenders,id',

            'fess_type_id'          => 'nullable|exists:add_fess_types,id',
            'transactions_type_id'  => 'nullable|exists:transactions_types,id',

            'student_book_number'   => 'nullable|string|max:255',
            'recipt_no'             => 'nullable|string|max:255',

            'monthly_fees'          => 'nullable|numeric|min:0',
            'boarding_fees'         => 'nullable|numeric|min:0',
            'management_fees'       => 'nullable|numeric|min:0',
            'exam_fees'             => 'nullable|numeric|min:0',
            'others_fees'           => 'nullable|numeric|min:0',
            'total_fees'            => 'nullable|numeric|min:0',

            'transactions_date'     => 'nullable|date',
            'account_id'            => 'nullable|exists:accounts,id',

            'class_id'              => 'nullable|exists:add_classes,id',
            'section_id'            => 'nullable|exists:add_sections,id',
            'months_id'             => 'nullable|exists:add_months,id',
            'academic_year_id'      => 'nullable|exists:add_academies,id',

            'created_by_id'         => 'nullable|exists:users,id',
            'note'                  => 'nullable|string|max:500',

            'isActived'             => 'nullable|boolean',
        ];

        for ($i = 1; $i <= 7; $i++) $rules["c_d_$i"] = 'nullable|numeric|min:0';
        for ($i = 1; $i <= 8; $i++) $rules["c_s_$i"] = 'nullable|string|max:255';
        for ($i = 1; $i <= 6; $i++) $rules["c_i_$i"] = 'nullable|integer|min:0';

        $validated = $request->validate($rules);

        // ✅ integrity
        $this->ensureSingleParty($validated);

        // ✅ student_fee type হলে total + ledger auto sync (আপনার fees edit ঠিকভাবে কাজ করবে)
        $studentFeeTypeId = $this->typeIdByKey('student_fee');
        $incomingTypeId = $validated['transactions_type_id'] ?? $tx->transactions_type_id;

        // total calc
        if (!isset($validated['total_fees'])) {
            // যদি fees field গুলো update হয়, total recalc
            $merged = array_merge($tx->toArray(), $validated);
            $validated['total_fees'] = $this->calcTotalFees($merged);
        }

        // যদি এটা student fee হয় (key বা id match) তাহলে debit/credit force করি
        if ($studentFeeTypeId && (int)$incomingTypeId === (int)$studentFeeTypeId) {
            $validated['debit']  = (float)$validated['total_fees'];
            $validated['credit'] = 0;
            $validated['doner_id']  = null;
            $validated['lender_id'] = null;
        }

        $validated['isActived'] = $validated['isActived'] ?? $tx->isActived;

        $tx->update($validated);

        // ✅ FIX redirect
        return redirect()->route('add_student_fees.index')->with('success', 'Transaction updated successfully!');
    }

    public function destroy($id)
    {
        $tx = Transactions::findOrFail($id);
        $tx->delete();

        // ✅ FIX redirect
        return redirect()->route('add_student_fees.list')->with('success', 'Transaction deleted successfully!');
    }

    public function all(Request $request)
    {
        // আপনার আগের কোডে month_id ছিল, কিন্তু schema এ months_id
        $students = Student::where('academic_year_id', $request->academic_year_id)
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->get();

        $transactions = [];

        return view('students.add-fees_copy', compact('students', 'transactions'));
    }
}
