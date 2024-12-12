<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookDetailResource;
use App\Http\Resources\CategoryResource;
use App\Models\Book;
use App\Models\Category;
use App\Supports\HandlePagination;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api'),
            new Middleware('verified'),
        ];
    }

    public function index(): AnonymousResourceCollection
    {
        $categories = QueryBuilder::for(Category::query())
            ->allowedFilters([
                AllowedFilter::exact('id'),
                'name'
            ])
            ->allowedIncludes(['books'])
            ->paginate((new HandlePagination())(request('perPage')))
            ->appends(request()->query());
        return CategoryResource::collection($categories);
    }
}
