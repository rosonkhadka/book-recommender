<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Resources\UserDetailResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    use ApiResponse;
    public function index()
    {
        return 'Users List';
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $data = $request->Validated();
        $user =  User::create($data);
        return $this->successResponse([
            'success' => true,
            'payload' => new UserDetailResource($user),
        ]);
    }
}
