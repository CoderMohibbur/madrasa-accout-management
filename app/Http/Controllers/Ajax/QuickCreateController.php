<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Student;
use App\Models\Donor;
use App\Models\Lender;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuickCreateController extends Controller
{
    public function storeAccount(Request $request)
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'account_number'  => ['nullable', 'string', 'max:255'],
            'account_details' => ['nullable', 'string', 'max:255'],
        ]);

        // account_number generate if empty + ensure unique
        $accountNumber = trim((string)($data['account_number'] ?? ''));
        if ($accountNumber === '') {
            do {
                $accountNumber = 'AC-' . strtoupper(Str::random(6));
            } while (Account::where('account_number', $accountNumber)->exists());
        } else {
            // if user provided, ensure unique
            if (Account::where('account_number', $accountNumber)->exists()) {
                return response()->json([
                    'message' => 'account_number already exists'
                ], 422);
            }
        }

        $acc = new Account();
        $acc->name = $data['name'];
        $acc->account_number = $accountNumber;
        $acc->account_details = $data['account_details'] ?? 'Quick added from Transaction Center';
        $acc->opening_balance = 0;
        $acc->current_balance = 0;
        $acc->isActived = true;
        $acc->isDeleted = false;
        $acc->save();

        return response()->json([
            'id'   => $acc->id,
            'name' => $acc->name,
        ], 201);
    }

    public function storeStudent(Request $request)
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'email'  => ['nullable', 'email', 'max:255'],
        ]);

        $s = new Student();
        $s->full_name = $data['name'];   // আপনার students table এ full_name আছে
        $s->mobile = $data['mobile'] ?? null;
        $s->email = $data['email'] ?? null;

        // required in migration
        $s->isActived = true;
        $s->isDeleted = false;

        // optional FKs (nullable) -> leave null
        $s->save();

        return response()->json([
            'id'   => $s->id,
            'name' => $s->full_name ?? ('Student #' . $s->id),
        ], 201);
    }

    public function storeDonor(Request $request)
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'email'  => ['nullable', 'email', 'max:255'],
        ]);

        $d = new Donor();
        $d->name = $data['name'];
        $d->mobile = $data['mobile'] ?? null;
        $d->email = $data['email'] ?? null;

        // required in migration
        $d->isActived = true;
        $d->isDeleted = false;

        // fees_type_id nullable -> leave null
        $d->save();

        return response()->json([
            'id'   => $d->id,
            'name' => $d->name ?? ('Donor #' . $d->id),
        ], 201);
    }

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
        $l->name = $data['name'];
        $l->phone = $data['phone'];
        $l->email = $data['email'];
        $l->address = $data['address'];
        $l->bank_detils = $data['bank_detils'];

        // required FK users_id
        $l->users_id = auth()->id() ?: 1;

        // required in migration
        $l->isActived = true;
        $l->isDeleted = false;

        $l->save();

        return response()->json([
            'id'   => $l->id,
            'name' => $l->name ?? ('Lender #' . $l->id),
        ], 201);
    }
}
