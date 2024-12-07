<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
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

class AuthController extends Controller implements HasMiddleware
{
    use ApiResponse;
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['login']),
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

    public function logout(): JsonResponse
    {
        Auth::logout(true);
        return $this->successResponse();
    }

    public function refresh(): JsonResponse
    {
        return $this->successResponse($this->respondWithToken(Auth::refresh(true, true)));
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        try {
            if (RateLimiter::tooManyAttempts(request()->ip(), 5)) {
                throw new ThrottleRequestsException(message: 'Too many attempts.', code: 429);
            }

            $status = Password::sendResetLink([
                'email' => $data['email']
            ]);

            if (Password::RESET_LINK_SENT !== $status) {
                RateLimiter::hit(request()->ip(), 5 * 60);

                switch ($status) {
                    case Password::INVALID_TOKEN:
                        $message = 'The provided password reset token is invalid!';
                        $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
                        throw new Exception($message, $statusCode);
                    case Password::RESET_THROTTLED:
                        $message = 'We have already sent a password reset link to your email address. Please check your inbox!';
                        $statusCode = Response::HTTP_TOO_MANY_REQUESTS;
                        throw new Exception($message, $statusCode);
                }
            }

            RateLimiter::clear(request()->ip());
            return $this->successResponse();

        } catch (Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        try {
            if (RateLimiter::tooManyAttempts(request()->ip(), 5)) {
                throw new ThrottleRequestsException(message: 'Too many attempts.', code: 429);
            }
            if ( ! $this->validatePasswordResetToken($data)) {
                RateLimiter::hit(request()->ip(), 5 * 60);
                throw new Exception('Invalid Token', 422);
            }
            $status = Password::reset(
                $data->toArray(),
                function (User $user, string $password): void {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ]);

                    $user->save();

                    //event(new PasswordReset($user));
                }
            );
            if (Password::PASSWORD_RESET !== $status) {
                throw new Exception('Oops! Something went wrong while trying to reset password', 422);
            }
            RateLimiter::clear(request()->ip());
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

    protected function validatePasswordResetToken($data): bool
    {
        $token = DB::table('password_reset_tokens')
            ->where('email', $data['email'])
            ->value('token');

        return Hash::check($data['token'], $token);
    }

}
