<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;
use Override;

final class FortifyServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        Fortify::ignoreRoutes();
    }

    public function boot(): void
    {
        $this->bootFortifyDefaults();
        $this->bootRateLimitingDefaults();

    }

    private function bootFortifyDefaults(): void
    {

        Fortify::loginView(fn () => Inertia::render('session/create', [
            'canResetPassword' => true,
            'status' => session('status'),
        ]));
        Fortify::twoFactorChallengeView(fn () => Inertia::render('user-two-factor-authentication-challenge/show', []));
        Fortify::confirmPasswordView(fn () => Inertia::render('user-password-confirmation/create', []));
    }

    private function bootRateLimitingDefaults(): void
    {
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by($request->string('email')->value().$request->ip()));
        RateLimiter::for('two-factor', fn (Request $request) => Limit::perMinute(5)->by($request->session()->get('login.id')));
    }
}
