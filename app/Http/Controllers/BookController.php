<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookDetailResource;
use App\Models\Book;
use App\Supports\HandlePagination;
use App\Traits\ApiResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BookController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:api'),
            new Middleware('verified'),
        ];
    }

    public function index(): AnonymousResourceCollection
    {
        $user = QueryBuilder::for(Book::query())
            ->allowedFilters([
                AllowedFilter::exact('id'),
                'title',
                'subtitle',
                'description',
                'page_count',
                'published_date',
                'language',
                'isbn',
                AllowedFilter::scope('categories')
            ])
            ->allowedIncludes(['categories'])
            ->paginate((new HandlePagination())(request('perPage')))
            ->appends(request()->query());
        return BookDetailResource::collection($user);
    }

    public function show(Book $book): BookDetailResource
    {
        $book->load('categories');
        return new BookDetailResource($book);
    }

}
