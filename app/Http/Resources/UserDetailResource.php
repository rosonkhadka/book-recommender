<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $hasBooks = $this->books->isNotEmpty();
        $hasCategories = $this->categories->isNotEmpty();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'social_provider' => $this->social_provider,
            'gender' => $this->gender,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'email_verified_at' => $this->email_verified_at,
            'user_preference' => [
                'status' => $hasBooks && $hasCategories,
                'books' => $hasBooks ? $this->books->pluck('id') : null,
                'categories' => $hasCategories ? $this->categories->pluck('id') : null,
            ],
        ];
    }
}
