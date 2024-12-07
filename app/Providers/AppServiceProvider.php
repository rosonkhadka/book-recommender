<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        Password::defaults(function () {
            $rule = Password::min(6);

            return $this->app->isProduction()
                ? $rule->mixedCase()->uncompromised()
                : $rule;
        });
    }
}
