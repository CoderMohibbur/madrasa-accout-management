<?php

namespace App\Http\Requests\Payments;

use App\Models\StudentFeeInvoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreManualBankPaymentRequest extends FormRequest
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
            'payer_name' => ['required', 'string', 'max:255'],
            'bank_reference' => ['required', 'string', 'max:255'],
            'payment_channel' => ['nullable', 'string', 'max:255'],
            'transferred_at' => ['required', 'date'],
            'evidence_url' => ['nullable', 'url', 'max:2000'],
            'note' => ['nullable', 'string', 'max:1000'],
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
