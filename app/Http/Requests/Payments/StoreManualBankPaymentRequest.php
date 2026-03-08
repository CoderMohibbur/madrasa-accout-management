<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class StoreManualBankPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'integer', 'exists:student_fee_invoices,id'],
            'payer_name' => ['required', 'string', 'max:255'],
            'bank_reference' => ['required', 'string', 'max:255'],
            'payment_channel' => ['nullable', 'string', 'max:255'],
            'transferred_at' => ['required', 'date'],
            'evidence_url' => ['nullable', 'url', 'max:2000'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
