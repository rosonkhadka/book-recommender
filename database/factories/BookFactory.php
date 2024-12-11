<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'subtitle' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'page_count' => fake()->numberBetween(50, 250),
            'published_date' => fake()->date('y-m-d'),
            'language' => fake()->languageCode(),
            'isbn' => fake()->isbn10(),
            'thumbnail_s' => fake()->imageUrl(),
            'thumbnail_m' => fake()->imageUrl(),
        ];
    }

    public function configure(): BookFactory|Factory
    {
        return $this->afterCreating(function ($book) {
            $category = Category::inRandomOrder()->first();

            if ($category) {
                $book->categories()->attach($category->id);
            }
        });
    }

}
