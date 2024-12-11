<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['*'];

    public function books(): MorphToMany
    {
        return $this->morphedByMany(Book::class, 'categoryable');
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'categoryable');
    }
}
