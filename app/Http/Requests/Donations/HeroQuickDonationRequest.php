<?php

namespace App\Http\Requests\Donations;

use App\Support\Donations\GuestDonationCategoryCatalog;
use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HeroQuickDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $fund = is_string($this->input('fund')) ? trim($this->input('fund')) : null;
        $phone = is_string($this->input('phone')) ? PhoneNumber::normalize($this->input('phone')) : null;
        $amount = is_string($this->input('amount')) ? trim($this->input('amount')) : $this->input('amount');

        $this->merge([
            'fund' => $fund !== '' ? $fund : null,
            'phone' => $phone !== '' ? $phone : null,
            'amount' => $amount !== '' ? $amount : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1'],
            'phone' => ['required', 'string', 'max:20'],
            'fund' => [
                'required',
                'string',
                Rule::in(GuestDonationCategoryCatalog::keys()),
                Rule::exists('donation_categories', 'key')->where(
                    fn ($query) => $query->where('is_active', true)
                ),
            ],
        ];
    }
}
