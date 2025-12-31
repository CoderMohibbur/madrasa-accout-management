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
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\TransactionsType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionCenterController extends Controller
{
    public function index(Request $request)
    {
        $q = Transactions::query()
            ->with(['student', 'donor', 'lender', 'account', 'type']);

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
                    ->orWhere('c_s_1', 'like', "%{$s}%"); // title for expense/income

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

        $accounts = Account::orderBy('name')->get();
        $types    = TransactionsType::orderBy('name')->get();

        $students = Student::query()
            ->with(['class', 'section', 'academicYear', 'feesType'])
            ->orderBy('id', 'desc')
            ->get();

        $donors   = Donor::orderBy('id', 'desc')->get();
        $lenders  = Lender::orderBy('id', 'desc')->get();

        // Bulk dropdown data
        $years     = AddAcademy::orderBy('id', 'desc')->get();
        $months    = AddMonth::orderBy('id', 'asc')->get();
        $classes   = AddClass::orderBy('id', 'asc')->get();
        $sections  = AddSection::orderBy('id', 'asc')->get();
        $feesTypes = AddFessType::orderBy('id', 'asc')->get();

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
            'feesTypes'
        ));
    }

    // =========================
    // Helpers
    // =========================

    private function ensureSingleParty(array $data): void
    {
        $count = 0;
        foreach (['student_id', 'doner_id', 'lender_id'] as $k) {
            if (!empty($data[$k])) {
                $count++;
            }
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

    /**
     * ✅ Robust type resolver:
     * - key match
     * - fallback by name/like (legacy)
     * - if found and key null -> auto set
     * - if still not found -> auto create type row
     */
    private function resolveTypeOrCreate(string $typeKey): TransactionsType
    {
        // 1) primary: key match
        $type = TransactionsType::query()->where('key', $typeKey)->first();
        if ($type) return $type;

        // 2) fallback: name match / legacy typo match
        $map = [
            'student_fee'    => ['Student Fee', 'Student Fees', 'Student Fess', 'Student Fesses'],
            'donation'       => ['Donation', 'Donar', 'Doner', 'Doner Donation'],
            'expense'        => ['Expense', 'Expens'],
            'income'         => ['Income'],
            'loan_taken'     => ['Loan', 'Loan Taken', 'Loan Take'],
            'loan_repayment' => ['Repayment', 'Loan Repayment', 'Loan Pay'],
        ];

        $names = $map[$typeKey] ?? [];

        $type = TransactionsType::query()
            ->when(!empty($names), fn($q) => $q->whereIn('name', $names))
            ->when(empty($names), function ($q) use ($typeKey) {
                $like = str_replace('_', ' ', $typeKey);
                $q->where('name', 'like', '%' . $like . '%');

                if ($typeKey === 'student_fee') {
                    $q->orWhere(function ($qq) {
                        $qq->where('name', 'like', '%student%')
                            ->where('name', 'like', '%fee%');
                    });
                }
            })
            ->first();

        // 3) found -> set key permanently
        if ($type) {
            if (empty($type->key)) {
                try {
                    $type->key = $typeKey;
                    $type->save();
                } catch (\Throwable $e) {
                    $existing = TransactionsType::query()->where('key', $typeKey)->first();
                    if ($existing) return $existing;
                }
            }
            return $type;
        }

        // 4) still not found -> create automatically
        $niceName = ucwords(str_replace('_', ' ', $typeKey));

        $new = new TransactionsType();
        $new->name = $niceName;
        $new->key = $typeKey;
        $new->isActived = true;
        $new->isDeleted = false;
        $new->save();

        return $new;
    }

    // =========================
    // Phase 2: Quick Store
    // =========================

    public function storeQuick(Request $request)
    {
        $typeKey = $request->input('type_key');
        if (!$typeKey) {
            return back()->with('error', 'type_key missing.');
        }

        // ✅ Always resolve or create type
        $type = $this->resolveTypeOrCreate($typeKey);

        // ✅ base validation (all types)
        $baseRules = [
            'type_key'          => 'required|string',
            'account_id'        => 'required|exists:accounts,id',
            'transactions_date' => 'nullable|date',
            'note'              => 'nullable|string|max:500',
            'recipt_no'         => 'nullable|string|max:255',
        ];

        $typeRules = [];

        if ($typeKey === 'student_fee') {
            $typeRules = [
                'student_id' => 'required|exists:students,id',

                // ✅ meta fields (month required; others optional + fallback)
                'months_id'        => 'required|exists:add_months,id',
                'academic_year_id' => 'nullable|exists:add_academies,id',
                'class_id'         => 'nullable|exists:add_classes,id',
                'section_id'       => 'nullable|exists:add_sections,id',
                'fess_type_id'     => 'nullable|exists:add_fess_types,id',

                // ✅ fees (including admission)
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
            ];
        } elseif ($typeKey === 'loan_taken') {
            $typeRules = [
                'lender_id' => 'required|exists:lenders,id',
                'amount'    => 'required|numeric|min:0.01',
            ];
        } elseif ($typeKey === 'loan_repayment') {
            $typeRules = [
                'lender_id' => 'required|exists:lenders,id',
                'amount'    => 'required|numeric|min:0.01',
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
        } else {
            return back()->with('error', "Unknown type_key: {$typeKey}");
        }

        $validated = $request->validate(array_merge($baseRules, $typeRules));

        // ✅ party integrity
        $this->ensureSingleParty($validated);

        // ✅ default date
        $txDate = $validated['transactions_date'] ?? Carbon::now()->toDateString();

        $txData = [
            'transactions_type_id' => $type->id,
            'account_id'           => $validated['account_id'],
            'transactions_date'    => $txDate,
            'created_by_id'        => auth()->id(),
            'isActived'            => true,
            'isDeleted'            => false,

            'note'      => $validated['note'] ?? null,
            'recipt_no' => $validated['recipt_no'] ?? null,

            'student_id' => $validated['student_id'] ?? null,
            'doner_id'   => $validated['doner_id'] ?? null,
            'lender_id'  => $validated['lender_id'] ?? null,
        ];

        // ✅ Type-wise save logic
        if ($typeKey === 'student_fee') {

            // ✅ load student for fallback meta + roll
            $student = Student::query()
                ->select('id', 'roll', 'class_id', 'section_id', 'academic_year_id', 'fees_type_id')
                ->find($validated['student_id']);

            // ✅ meta (required/optional + fallback)
            $txData['months_id']        = $validated['months_id'];
            $txData['academic_year_id'] = $validated['academic_year_id'] ?? ($student?->academic_year_id);
            $txData['class_id']         = $validated['class_id'] ?? ($student?->class_id);
            $txData['section_id']       = $validated['section_id'] ?? ($student?->section_id);
            $txData['fess_type_id']     = $validated['fess_type_id'] ?? ($student?->fees_type_id);

            // ✅ Save roll কোথায়? (transactions table এ roll নাই) -> custom int field ব্যবহার
            $txData['c_i_1'] = $student?->roll;

            // ✅ Admission fee কোথায়? -> custom decimal field ব্যবহার
            $txData['c_d_1'] = $validated['admission_fee'] ?? null;

            $txData['student_book_number'] = $validated['student_book_number'] ?? null;

            // fees parts
            $txData['monthly_fees']    = $validated['monthly_fees'] ?? null;
            $txData['boarding_fees']   = $validated['boarding_fees'] ?? null;
            $txData['management_fees'] = $validated['management_fees'] ?? null;
            $txData['exam_fees']       = $validated['exam_fees'] ?? null;
            $txData['others_fees']     = $validated['others_fees'] ?? null;

            // ✅ total calc: existing helper + admission_fee
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

            // ✅ Ledger: cash in
            $txData['debit']  = $total;
            $txData['credit'] = 0;

            // ✅ ensure other parties null
            $txData['doner_id']  = null;
            $txData['lender_id'] = null;
        }

        if ($typeKey === 'donation') {
            $amount = (float)$validated['amount'];

            $txData['debit']  = $amount;
            $txData['credit'] = 0;

            // clean irrelevant fields
            $txData['total_fees'] = null;
            $txData['student_id'] = null;
            $txData['lender_id']  = null;
        }

        if ($typeKey === 'loan_taken') {
            $amount = (float)$validated['amount'];

            $txData['debit']  = $amount; // cash in
            $txData['credit'] = 0;

            $txData['total_fees'] = null;
            $txData['student_id'] = null;
            $txData['doner_id']   = null;
        }

        if ($typeKey === 'loan_repayment') {
            $amount = (float)$validated['amount'];

            $txData['debit']  = 0;
            $txData['credit'] = $amount; // cash out

            $txData['total_fees'] = null;
            $txData['student_id'] = null;
            $txData['doner_id']   = null;
        }

        if ($typeKey === 'expense') {
            $amount = (float)$validated['amount'];
            $title  = $validated['title'];

            // ✅ title save
            $txData['c_s_1']  = $title;
            $txData['debit']  = 0;
            $txData['credit'] = $amount;

            $txData['catagory_id'] = $validated['catagory_id'] ?? null;
            $txData['expens_id']   = $validated['expens_id'] ?? null;

            // clean irrelevant
            $txData['total_fees'] = null;
            $txData['student_id'] = null;
            $txData['doner_id']   = null;
            $txData['lender_id']  = null;
        }

        if ($typeKey === 'income') {
            $amount = (float)$validated['amount'];
            $title  = $validated['title'];

            $txData['c_s_1']  = $title;
            $txData['debit']  = $amount;
            $txData['credit'] = 0;

            $txData['catagory_id'] = $validated['catagory_id'] ?? null;
            $txData['income_id']   = $validated['income_id'] ?? null;

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
}
