<?php

declare(strict_types=1);

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPasswordController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserTwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified:tenant.auth.verification.notice'])
    ->group(function (): void {

        Route::delete('user', [UserController::class, 'destroy'])
            ->name('destroy');

        Route::prefix('user.settings')
            ->name('settings.')
            ->group(function (): void {

                Route::redirect('/', '/settings/profile');
                Route::get('profile', [UserProfileController::class, 'edit'])
                    ->name('profile.edit');
                Route::patch('profile', [UserProfileController::class, 'update'])
                    ->name('profile.update');

                // User Password...
                Route::get('password', [UserPasswordController::class, 'edit'])->name('password.edit');
                Route::put('password', [UserPasswordController::class, 'update'])
                    ->middleware('throttle:6,1')
                    ->name('password.update');

                // User Two-Factor Authentication...
                Route::get('two-factor', [UserTwoFactorAuthenticationController::class, 'show'])
                    ->name('two-factor.show');

            });

    });
