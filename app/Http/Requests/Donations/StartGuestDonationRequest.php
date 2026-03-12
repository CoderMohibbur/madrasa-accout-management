<?php

namespace App\Http\Requests\Donations;

use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StartGuestDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $name = is_string($this->input('name')) ? trim($this->input('name')) : null;
        $email = is_string($this->input('email')) ? Str::lower(trim($this->input('email'))) : null;
        $phone = is_string($this->input('phone')) ? PhoneNumber::normalize($this->input('phone')) : null;
        $buttonAmount = is_string($this->input('amount')) ? trim($this->input('amount')) : $this->input('amount');
        $customAmount = is_string($this->input('custom_amount')) ? trim($this->input('custom_amount')) : $this->input('custom_amount');
        $category = is_string($this->input('category')) ? trim($this->input('category')) : $this->input('category');

        $this->merge([
            'category' => $category !== '' ? $category : null,
            'amount' => $buttonAmount !== null && $buttonAmount !== '' ? $buttonAmount : $customAmount,
            'custom_amount' => $customAmount !== '' ? $customAmount : null,
            'name' => $name !== '' ? $name : null,
            'email' => $email !== '' ? $email : null,
            'phone' => $phone !== '' ? $phone : null,
            'anonymous_display' => $this->boolean('anonymous_display'),
            'checkout_now' => $this->boolean('checkout_now'),
        ]);
    }

    public function rules(): array
    {
        return [
            'category' => [
                'required',
                'string',
                Rule::exists('donation_categories', 'key')->where(
                    fn ($query) => $query->where('is_active', true)
                ),
            ],
            'amount' => ['required', 'numeric', 'min:1'],
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'anonymous_display' => ['nullable', 'boolean'],
            'checkout_now' => ['nullable', 'boolean'],
        ];
    }
}
