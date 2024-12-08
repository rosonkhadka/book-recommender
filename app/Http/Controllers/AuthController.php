<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\GoogleLoginRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Services\GoogleLogin;
use App\Services\ResetPassword;
use App\Traits\ApiResponse;
use Auth;
use DB;
use Exception;
use Hash;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Password;
use RateLimiter;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Google_Client;

class AuthController extends Controller implements HasMiddleware
{
    use ApiResponse;
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['login', 'googleLogin', 'forgotPassword']),
        ];
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try{
            $data = $request->validated();
            if (RateLimiter::tooManyAttempts(request()->ip(), 5)) {
                throw new ThrottleRequestsException(message: 'Too many attempts.', code: 429);
            }

            $token = Auth::guard('api')->attempt(
                [
                    'email' => $data['email'],
                    'password' => $data['password'],
                ],
            );
            if ( ! $token) {
                RateLimiter::hit(request()->ip(), 5 * 60);
                throw new AuthenticationException('Invalid Credentials');
            }
            RateLimiter::clear(request()->ip());
            return $this->successResponse($this->respondWithToken($token));
        }catch(Throwable $th){
            return $this->errorResponse($th);
        }
    }

    public function googleLogin(GoogleLoginRequest $request, GoogleLogin $service): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $service->googleLogin($data);
            $token = auth()->login($user);
            return $this->successResponse($this->respondWithToken($token));
        } catch (Throwable $th) {
            return $this->errorResponse($th);
        }
    }


    public function logout(): JsonResponse
    {
        Auth::logout(true);
        return $this->successResponse();
    }

    public function refresh(): JsonResponse
    {
        return $this->successResponse($this->respondWithToken(Auth::refresh(true, true)));
    }

    public function forgotPassword(ForgotPasswordRequest $request, ResetPassword $service): JsonResponse
    {
        try {
            $data = $request->validated();
            $service->forgotPassword($data);
            return $this->successResponse();
        } catch (Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function resetPassword(ResetPasswordRequest $request, ResetPassword $service): JsonResponse
    {
        try {
            $data = $request->validated();
            $service->resetPassword($data);
            return $this->successResponse();
        } catch (Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    protected function respondWithToken($token): array
    {
        return [
            'success' => true,
            'payload' => Auth::user(),
            'token' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                /** @phpstan-ignore-next-line */
                'expires_in' => Auth::factory()->getTTL() * 60
            ]

        ];
    }


}
