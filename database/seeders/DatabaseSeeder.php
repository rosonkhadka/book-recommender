<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $categories = Category::factory(50)->create();

        $user = User::factory()->create(
            [
                'email' => 'rosonkhadka@gmail.com',
            ]
        );

        //$user->categories()->attach(
        //    $categories->random(5)->pluck('id')->toArray()
        //);

        $books = Book::factory(100)->create()->each(function($book) use ($categories) {
            $book->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );
        });

        //$user->books()->attach(
        //    $books->random(10)->pluck('id')->toArray() // Attach 10 random books
        //);
    }
}
