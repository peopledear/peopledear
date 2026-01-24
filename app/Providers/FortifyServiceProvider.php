<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;
use Override;

use function App\tenant_route;

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
        $this->bootPasswordResetDefaults();
        $this->bootEmailVerificationDefaults();
    }

    private function bootPasswordResetDefaults(): void
    {
        ResetPassword::createUrlUsing(function (mixed $notifiable, string $token): string {
            /** @var User $notifiable */
            return tenant_route(
                name: 'tenant.auth.password.reset',
                tenant: $notifiable->organization,
                parameters: ['token' => $token],
            );
        });
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

    private function bootEmailVerificationDefaults(): void
    {
        VerifyEmail::createUrlUsing(function (mixed $notifiable): string {
            /** @var User $notifiable */
            return URL::temporarySignedRoute(
                'tenant.auth.verification.verify',
                Date::now()->addMinutes(Config::integer('auth.verification.expire', 60)),
                [
                    'tenant' => $notifiable->organization->identifier,
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });
    }
}
