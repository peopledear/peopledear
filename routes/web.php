<?php

declare(strict_types=1);

use App\Http\Controllers\AcceptInvitationController;
use App\Http\Controllers\ActivateUserController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeactivateUserController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\Profile\UserAvatarController;
use App\Http\Controllers\Profile\UserProfileController;
use App\Http\Controllers\ResendInvitationController;
use App\Http\Controllers\UpdateUserRoleController;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): View => view('welcome'))
    ->name('welcome');

Route::middleware(['auth'])->group(function (): void {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::as('profile.')->group(function (): void {

        Route::get('/profile', [UserProfileController::class, 'index'])
            ->name('index');

        Route::put('/profile', [UserProfileController::class, 'update'])
            ->name('update');

        Route::delete('/profile/avatar', [UserAvatarController::class, 'destroy'])
            ->name('avatar.destroy');
    });

});

// Admin routes (protected by auth + admin middleware)
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function (): void {

        // User management
        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');

        // Invitation management
        Route::post('/invitations', [InvitationController::class, 'store'])
            ->name('invitations.store');

        Route::post('/invitations/{invitation}/resend', ResendInvitationController::class)
            ->name('invitations.resend');

        Route::delete('/invitations/{invitation}', [InvitationController::class, 'destroy'])
            ->name('invitations.destroy');

        // User activation/deactivation
        Route::post('/users/{user}/activate', ActivateUserController::class)
            ->name('users.activate');

        Route::post('/users/{user}/deactivate', DeactivateUserController::class)
            ->name('users.deactivate');

        // Role management
        Route::patch('/users/{user}/role', UpdateUserRoleController::class)
            ->name('users.role.update');

    });

// Public invitation acceptance routes
Route::middleware(['guest'])->group(function (): void {

    Route::get('/invitation/{token}', [AcceptInvitationController::class, 'show'])
        ->name('invitation.show');

    Route::post('/invitation/{token}', [AcceptInvitationController::class, 'store'])
        ->name('invitation.accept');

});

Route::as('auth.')->group(function (): void {

    Route::middleware(['guest'])->group(function (): void {

        Route::get('/login', [LoginController::class, 'index'])
            ->name('login.index');

        Route::post('/login', [LoginController::class, 'store'])
            ->name('login.store');

    });

    Route::middleware(['auth'])->group(function (): void {

        Route::post('/logout', [LogoutController::class, 'store'])
            ->name('logout.store');

    });

});

if (app()->environment('testing')) {
    // Test route for AdminMiddleware
    Route::get('/admin/test', fn () => response()->json(['message' => 'Admin access granted']))->middleware(['auth', 'admin']);
}
