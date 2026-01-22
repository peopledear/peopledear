<?php

declare(strict_types=1);

use App\Http\Controllers\EmployeeOverviewController;
use App\Http\Controllers\EmployeeTimeOffController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified:tenant.auth.verification.notice'])
    ->group(function (): void {
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
