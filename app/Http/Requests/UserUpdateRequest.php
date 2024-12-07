<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|min:3|max:255',
            //'email' => [
            //    'required',
            //    'email',
            //    Rule::unique('users')->ignore($this->route('user')),
            //],
            'gender' => 'nullable',
        ];
    }
}
