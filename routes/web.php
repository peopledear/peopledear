<?php

declare(strict_types=1);

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::tenanted(function (): void {
    Route::as('tenant.')
        ->group(base_path('routes/tenant.php'));
}, 'subdomain', 'tenant');

$marketingRoutes = static function (): void {
    Route::get('/', fn () => Inertia::render('welcome', []))
        ->name('home');

    Route::middleware(['auth', 'verified'])->group(function (): void {
        Route::get('dashboard', fn () => Inertia::render('dashboard', []))
            ->name('dashboard');

        Route::get('organization-required', fn () => Inertia::render('organization-required', []))
            ->name('organization-required');

        Route::prefix('org')
            ->as('org.')->group(function (): void {
                Route::as('employees.')
                    ->prefix('employees')
                    ->group(function (): void {
                        Route::get('/', [EmployeeController::class, 'index'])
                            ->name('index');
                    });
            });
    });

    Route::as('auth.')
        ->group(__DIR__.'/auth.php');
};

$marketingDomainConfig = config('multitenancy.marketing_domains', []);

if (! is_array($marketingDomainConfig)) {
    $marketingDomainConfig = $marketingDomainConfig !== ''
        ? explode(',', (string) $marketingDomainConfig)
        : [];
}

foreach ($marketingDomainConfig as $configIndex => $configValue) {
    $marketingDomainConfig[$configIndex] = mb_trim((string) $configValue);
}

$marketingDomains = array_values(array_unique(array_filter([
    ...$marketingDomainConfig,
    config()->string('multitenancy.tenanted_domain'),
    (string) parse_url((string) config('app.url'), PHP_URL_HOST),
])));

if ($marketingDomains === []) {
    $marketingRoutes();
} else {
    foreach ($marketingDomains as $marketingDomain) {
        if ($marketingDomain === '') {
            continue;
        }

        Route::domain($marketingDomain)->group($marketingRoutes);
    }
}
