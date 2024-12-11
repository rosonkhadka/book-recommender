<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'description' => $this->description,
            'page_count' => $this->page_count,
            'published_date' => $this->published_date,
            'language' => $this->language,
            'isbn' => $this->isbn,
            'thumbnail_s' => $this->thumbnail_s,
            'thumbnail_m' => $this->thumbnail_m,
            'categories' => $this->relationLoaded('categories')
                ? $this->categories->map(fn($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                ])
                : null,
        ];
    }
}
