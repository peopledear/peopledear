<?php

declare(strict_types=1);

use App\Http\Controllers\DeleteNotificationController;
use App\Http\Controllers\DropdownNotificationController;
use App\Http\Controllers\EmployeeOverviewController;
use App\Http\Controllers\EmployeeTimeOffController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MarkAllNotificationsAsReadController;
use App\Http\Controllers\MarkNotificationAsReadController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationEmployeeController;
use App\Http\Controllers\OrganizationTimeOffTypesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPasswordController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserTwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::domain(config()->string('multitenancy.tenanted_domain'))->group(function (): void {

    Route::get('/', fn () => Inertia::render('welcome', []))
        ->name('home');

    Route::middleware(['auth', 'verified'])->group(function (): void {

        Route::get('dashboard', fn () => Inertia::render('dashboard', []))
            ->name('dashboard');

        Route::as('employee.')->group(function (): void {

            Route::get('/overview', [EmployeeOverviewController::class, 'index'])
                ->name('overview');

            Route::as('time-offs.')
                ->prefix('time-offs')
                ->group(function (): void {

                    Route::get('/', [EmployeeTimeOffController::class, 'index'])
                        ->name('index');

                    Route::get('/create', [EmployeeTimeOffController::class, 'create'])
                        ->name('create');

                    Route::post('/store', [EmployeeTimeOffController::class, 'store'])
                        ->name('store');

                });

        });

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

                // Organization Settings...

                Route::prefix('settings')->as('settings.')->group(function (): void {

                    Route::prefix('time-off-types')
                        ->as('time-off-types.')->group(function (): void {

                            Route::post('/', [OrganizationTimeOffTypesController::class, 'store'])
                                ->name('store');

                            Route::get('/', [OrganizationTimeOffTypesController::class, 'index'])
                                ->name('index');

                            Route::get('create', [OrganizationTimeOffTypesController::class, 'create'])
                                ->name('create');

                        });

                });

                Route::post('locations', [LocationController::class, 'store'])
                    ->name('locations.store');

                Route::put('locations/{location}', [LocationController::class, 'update'])
                    ->name('locations.update');

                Route::delete('locations/{location}', [LocationController::class, 'destroy'])
                    ->name('locations.destroy');

            });

    });

    Route::middleware('auth')->group(function (): void {
        // Notifications...

        Route::get('notifications/dropdown', [DropdownNotificationController::class, 'index'])
            ->name('notifications.dropdown');

        Route::post('notifications/{notification}/mark-read', [MarkNotificationAsReadController::class, 'store'])
            ->name('notifications.mark-read');
        Route::post('notifications/mark-all-read', [MarkAllNotificationsAsReadController::class, 'store'])
            ->name('notifications.mark-all-read');
        Route::delete('notifications/{notification}', [DeleteNotificationController::class, 'destroy'])
            ->name('notifications.destroy');

        // User...
        Route::delete('user', [UserController::class, 'destroy'])->name('user.destroy');

        // User Profile...
        Route::redirect('settings', '/settings/profile');
        Route::get('settings/profile', [UserProfileController::class, 'edit'])->name('user-profile.edit');
        Route::patch('settings/profile', [UserProfileController::class, 'update'])->name('user-profile.update');

        // User Password...
        Route::get('settings/password', [UserPasswordController::class, 'edit'])->name('password.edit');
        Route::put('settings/password', [UserPasswordController::class, 'update'])
            ->middleware('throttle:6,1')
            ->name('password.update');

        // Appearance...
        Route::get('settings/appearance', fn () => Inertia::render('appearance/update'))->name('appearance.edit');

        // User Two-Factor Authentication...
        Route::get('settings/two-factor', [UserTwoFactorAuthenticationController::class, 'show'])
            ->name('two-factor.show');
    });

});

Route::as('auth.')
    ->group(__DIR__.'/auth.php');

Route::middleware(['sprout.tenanted:subdomain,tenant'])->group(function (): void {
    Route::middleware('web')
        ->as('tenant.')
        ->group(base_path('routes/tenant.php'));
});
