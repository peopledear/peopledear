<?php

declare(strict_types=1);

use App\Http\Controllers\ApprovalQueueController;
use App\Http\Controllers\DeleteNotificationController;
use App\Http\Controllers\DropdownNotificationController;
use App\Http\Controllers\EmployeeOverviewController;
use App\Http\Controllers\EmployeeTimeOffController;
use App\Http\Controllers\MarkAllNotificationsAsReadController;
use App\Http\Controllers\MarkNotificationAsReadController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationEmployeeController;
use App\Http\Controllers\OrganizationOfficeController;
use App\Http\Controllers\OrganizationTimeOffTypesController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserEmailResetNotification;
use App\Http\Controllers\UserEmailVerification;
use App\Http\Controllers\UserEmailVerificationNotificationController;
use App\Http\Controllers\UserPasswordController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserTwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn() => Inertia::render('welcome', []))->name('home');

Route::middleware(['auth', 'verified'])->group(function (): void {

    Route::get('dashboard', fn() => Inertia::render('dashboard', []))
        ->name('dashboard');

    Route::as('employee.')->group(function (): void {

        Route::get('/overview', [EmployeeOverviewController::class, 'index'])
            ->name('overview');

        Route::as('time-offs.')->prefix('time-offs')->group(function (): void {

            Route::get('/', [EmployeeTimeOffController::class, 'index'])
                ->name('index');

            Route::get('/create', [EmployeeTimeOffController::class, 'create'])
                ->name('create');

            Route::post('/store', [EmployeeTimeOffController::class, 'store'])
                ->name('store');

        });

    });

    Route::get('organization-required', fn() => Inertia::render('organization-required', []))
        ->name('organization-required');

    Route::middleware(['role:people_manager|owner|manager'])
        ->prefix('org')
        ->as('org.')->group(function (): void {

            Route::get('/', [OrganizationController::class, 'index'])
                ->middleware('can:employees.view')
                ->name('overview');

            Route::as('employees.')->prefix('employees')
                ->group(function (): void {
                    Route::get('/', [OrganizationEmployeeController::class, 'index'])
                        ->name('index');
                });

            Route::as('approvals.')->prefix('approvals')
                ->group(function (): void {
                    Route::get('/', [ApprovalQueueController::class, 'index'])
                        ->name('index');
                    Route::post('/{approval}/approve', [ApprovalQueueController::class, 'approve'])
                        ->name('approve');
                    Route::post('/{approval}/reject', [ApprovalQueueController::class, 'reject'])
                        ->name('reject');
                });

            Route::get('create', [OrganizationController::class, 'create'])
                ->name('create');

            Route::post('create', [OrganizationController::class, 'store'])
                ->name('store');

            // Organization Settings...
            Route::get('settings', [OrganizationController::class, 'edit'])
                ->middleware('can:organizations.edit')
                ->name('settings.organization.edit');

            Route::put('settings/organization', [OrganizationController::class, 'update'])
                ->middleware('can:organizations.edit')
                ->name('settings.organization.update');

            Route::post('offices', [OrganizationOfficeController::class, 'store'])
                ->middleware('can:organizations.edit')
                ->name('offices.store');

            Route::put('offices/{office}', [OrganizationOfficeController::class, 'update'])
                ->middleware('can:organizations.edit')
                ->name('offices.update');

            Route::delete('offices/{office}', [OrganizationOfficeController::class, 'destroy'])
                ->middleware('can:organizations.edit')
                ->name('offices.destroy');

            Route::get('time-off-types', [OrganizationTimeOffTypesController::class, 'index'])
                ->name('time-off-types.index');

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
    Route::get('settings/appearance', fn() => Inertia::render('appearance/update'))->name('appearance.edit');

    // User Two-Factor Authentication...
    Route::get('settings/two-factor', [UserTwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');
});

Route::middleware('guest')->group(function (): void {
    // User...
    Route::get('register', [UserController::class, 'create'])
        ->name('register');
    Route::post('register', [UserController::class, 'store'])
        ->name('register.store');

    // User Password...
    Route::get('reset-password/{token}', [UserPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [UserPasswordController::class, 'store'])
        ->name('password.store');

    // User Email Reset Notification...
    Route::get('forgot-password', [UserEmailResetNotification::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [UserEmailResetNotification::class, 'store'])
        ->name('password.email');

    // Session...
    Route::get('login', [SessionController::class, 'create'])
        ->name('login');
    Route::post('login', [SessionController::class, 'store'])
        ->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    // User Email Verification...
    Route::get('verify-email', [UserEmailVerificationNotificationController::class, 'create'])
        ->name('verification.notice');
    Route::post('email/verification-notification', [UserEmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // User Email Verification...
    Route::get('verify-email/{id}/{hash}', [UserEmailVerification::class, 'update'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Session...
    Route::post('logout', [SessionController::class, 'destroy'])
        ->name('logout');
});
