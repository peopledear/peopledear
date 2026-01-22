<?php

declare(strict_types=1);

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationEmployeeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::tenanted(function (): void {
    Route::as('tenant.')
        ->group(base_path('routes/tenant.php'));
}, 'subdomain', 'tenant');

Route::domain(config()->string('multitenancy.tenanted_domain'))->group(function (): void {

    Route::get('/', fn () => Inertia::render('welcome', []))
        ->name('home');

    Route::middleware(['auth', 'verified'])->group(function (): void {

        Route::get('dashboard', fn () => Inertia::render('dashboard', []))
            ->name('dashboard');

        Route::get('organization-required', fn () => Inertia::render('organization-required', []))
            ->name('organization-required');

        Route::prefix('org')
            ->as('org.')->group(function (): void {

                Route::as('employees.')->prefix('employees')
                    ->group(function (): void {
                        Route::get('/', [OrganizationEmployeeController::class, 'index'])
                            ->name('index');
                    });

                Route::get('create', [OrganizationController::class, 'create'])
                    ->name('create');

                Route::post('create', [OrganizationController::class, 'store'])
                    ->name('store');

            });

    });

    Route::as('auth.')
        ->group(__DIR__.'/auth.php');

});
