<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserPreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'books' => 'required|array',
            'books.*.id' => 'required|exists:books,id',
            'books.*.rating' => 'required|integer|min:1|max:5',
        ];
    }
}
