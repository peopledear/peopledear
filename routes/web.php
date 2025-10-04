<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): View => view('welcome'))
    ->name('welcome');

Route::as('auth.')->group(function (): void {

    Route::middleware(['guest'])->group(function (): void {

        Route::get('/login', [LoginController::class, 'index'])
            ->name('login.index');

        Route::post('/login', [LoginController::class, 'store'])
            ->name('login.store');

    });

});
