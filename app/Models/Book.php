<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Book extends Model
{
    use HasFactory;
    protected $fillable = ['*'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('rating');
    }
    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }

    //  books?filter[categories]=Fiction,Adventure
    public function scopeCategories($query, $categoryNames)
    {
        $categoryNames = (array) $categoryNames;

        return $query->whereHas('categories', function ($query) use ($categoryNames) {
            $query->whereIn('name', $categoryNames);
        });
    }
}
