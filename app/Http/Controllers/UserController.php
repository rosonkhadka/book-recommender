<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserDetailResource;
use App\Mail\EmailVerificationMail;
use App\Models\User;
use App\Supports\HandlePagination;
use App\Traits\ApiResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Throwable;

class UserController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:api',  except: ['store']),
            new Middleware('verified',  except: ['store']),
        ];
    }
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

    public function store(UserStoreRequest $request): JsonResponse|Throwable
    {
        try{
            $data = $request->Validated();
            $user =  User::create($data);
            $this->sendVerificationEmail($user);

            return $this->successResponse([
                'success' => true,
                'message' => 'User Successfully Created',
                'payload' => new UserDetailResource($user),
            ]);
        }catch(Throwable $th){
            return $this->errorResponse($th);
        }
    }

    public function show(User $user): UserDetailResource
    {
        return new UserDetailResource($user);
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

    private function sendVerificationEmail(User $user): void
    {
        $baseUrl = config('app.frontend_url');
        $token = bin2hex(random_bytes(20));
        $fullUrl = $baseUrl . '/verify-email?email=' . $user->email . '&token=' . $token;
        DB::table('user_email_verification')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
        ]);
        Mail::to($user)->send(new EmailVerificationMail($fullUrl));
    }
}
