<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class InitiateShurjopayPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'integer', 'exists:student_fee_invoices,id'],
        ];
    }
}
