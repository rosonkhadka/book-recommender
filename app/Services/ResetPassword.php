<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpFoundation\Response;

class ResetPassword
{
    /**
     * @throws \Exception
     */
    public function forgotPassword(Array $data): void
    {
        if (RateLimiter::tooManyAttempts(request()->ip(), 5)) {
            throw new ThrottleRequestsException(message: 'Too many attempts.', code: 429);
        }

        $status = Password::sendResetLink([
            'email' => $data['email']
        ]);

        if (Password::RESET_LINK_SENT !== $status) {
            RateLimiter::hit(request()->ip(), 5 * 60);
            $this->handleException($status);
        }
        RateLimiter::clear(request()->ip());
    }

    /**
     * @throws \Exception
     */
    public function resetPassword(Array $data): void
    {
        if (RateLimiter::tooManyAttempts(request()->ip(), 5)) {
            throw new ThrottleRequestsException(message: 'Too many attempts.', code: 429);
        }
        if ( ! $this->validatePasswordResetToken($data)) {
            RateLimiter::hit(request()->ip(), 5 * 60);
            throw new Exception('Invalid Token', 422);
        }
        $status = Password::reset(
            $data,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ]);

                $user->save();

                event(new PasswordReset($user));
            }
        );
        if (Password::PASSWORD_RESET !== $status) {
            throw new Exception('Oops! Something went wrong while trying to reset password', 422);
        }
        RateLimiter::clear(request()->ip());
    }

    /**
     * @throws \Exception
     */
    public function handleException(string $status): void
    {
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

    protected function validatePasswordResetToken(Array $data): bool
    {
        $token = DB::table('password_reset_tokens')
            ->where('email', $data['email'])
            ->value('token');

        return Hash::check($data['token'], $token);
    }
}
