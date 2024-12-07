<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users',
            'social_provider' => 'nullable',
            'social_provider_id' => 'nullable',
            'gender' => 'nullable',
            'password' => ['required', Password::defaults()],
        ];
    }
}
