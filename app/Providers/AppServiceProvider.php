<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Override;
use Stripe\StripeClient;

final class AppServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        Request::macro('wantsDropdown', function (): bool {
            /** @var Request $this */
            return (bool) $this->header('X-Dropdown');
        });

        $this->app->singleton(StripeClient::class, fn (): StripeClient => new StripeClient(
            config()->string('services.stripe.secret'),
        ));
    }

    public function boot(): void
    {
        $this->bootModelsDefaults();
        $this->bootPasswordDefaults();
    }

    private function bootModelsDefaults(): void
    {
        Model::unguard();
    }

    private function bootPasswordDefaults(): void
    {
        Password::defaults(fn () => app()->isLocal() || app()->runningUnitTests() ? Password::min(12)->max(255) : Password::min(12)->max(255)->uncompromised());
    }
}
