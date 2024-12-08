<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Mail\ResetPasswordMail;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        ResetPassword::toMailUsing(function ($notifiable, $token) {
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
