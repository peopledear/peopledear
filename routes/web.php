<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Profile\UserAvatarController;
use App\Http\Controllers\Profile\UserProfileController;
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

    Route::as('users.')->group(function (): void {

        Route::get('/users', [UserController::class, 'index'])
            ->name('index');

    });

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
