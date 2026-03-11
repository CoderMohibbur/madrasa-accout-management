<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class OpenRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => Str::lower(trim((string) $this->input('email'))),
            'phone' => PhoneNumber::normalize($this->input('phone')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'regex:/^\+?[0-9]{8,15}$/'],
            'intent' => ['nullable', Rule::in(['public', 'donor', 'guardian'])],
        ];
    }

    public function registrationIntent(): string
    {
        $intent = (string) $this->input('intent', 'public');

        return in_array($intent, ['public', 'donor', 'guardian'], true)
            ? $intent
            : 'public';
    }
}
