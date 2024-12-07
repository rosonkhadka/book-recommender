<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserDetailResource;
use App\Models\User;
use App\Supports\HandlePagination;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    use ApiResponse;
    public function index(): AnonymousResourceCollection
    {
        $user = QueryBuilder::for(User::query())
            ->allowedFilters([
                AllowedFilter::exact('id'),
                'name',
                'email',
                AllowedFilter::exact('name'),
            ])
            ->paginate((new HandlePagination())(request('perPage')))
            ->appends(request()->query());
        return UserDetailResource::collection($user);
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $data = $request->Validated();
        $user =  User::create($data);
        return $this->successResponse([
            'success' => true,
            'message' => 'User Successfully Created',
            'payload' => new UserDetailResource($user),
        ]);
    }

    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        $data = $request->Validated();
        $user->update($data);
        $user->refresh();
        return $this->successResponse([
            'success' => true,
            'message' => 'User Successfully Updated',
            'payload' => new UserDetailResource($user),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        $user->refresh();
        return $this->successResponse([
            'success' => true,
            'message' => 'User Successfully Deleted',
            'payload' => new UserDetailResource($user),
        ]);
    }
}
