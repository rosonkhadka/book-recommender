<?php

declare(strict_types=1);

namespace App\Providers;

use App\Mail\EmailVerificationMail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Mail\ResetPasswordMail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        // Email Verification Email
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new EmailVerificationMail($notifiable->email, $url))
                ->to($notifiable->email);
        });

        // Reset Password Email
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            return (new ResetPasswordMail($notifiable->email, $token))
                ->to($notifiable->email);
        });

        Password::defaults(function () {
            $rule = Password::min(6);

            return $this->app->isProduction()
                ? $rule->mixedCase()->uncompromised()
                : $rule;
        });
    }
}
