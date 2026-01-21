<?php

declare(strict_types=1);

use App\Http\Controllers\RegistrationController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (Router $router): void {

    // User...
    Route::get('register', [RegistrationController::class, 'create'])
        ->name('register');
    Route::post('register', [RegistrationController::class, 'store'])
        ->name('register.store');

});
