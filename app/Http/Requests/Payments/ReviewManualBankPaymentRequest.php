<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class ReviewManualBankPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'matched_bank_reference' => ['nullable', 'string', 'max:255'],
            'decision_note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
