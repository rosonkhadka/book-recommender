<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;


class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'exists:password_reset_tokens'],
            'token' => 'required',
            'password' => [
                'required',
                'confirmed',
                Password::defaults()
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'The email provided is invalid.',
        ];
    }
}
