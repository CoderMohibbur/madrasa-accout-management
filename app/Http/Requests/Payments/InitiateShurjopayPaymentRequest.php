<?php

namespace App\Http\Requests\Payments;

use App\Models\StudentFeeInvoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class InitiateShurjopayPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        $invoice = $this->resolveInvoice();

        if (! $invoice) {
            return true;
        }

        return Gate::forUser($user)->allows('pay', $invoice);
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'integer', 'exists:student_fee_invoices,id'],
        ];
    }

    private function resolveInvoice(): ?StudentFeeInvoice
    {
        $invoiceId = $this->integer('invoice_id');

        if ($invoiceId <= 0) {
            return null;
        }

        return StudentFeeInvoice::query()->find($invoiceId);
    }
}
