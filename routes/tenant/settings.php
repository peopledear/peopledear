<?php

declare(strict_types=1);

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\TimeOffTypesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified:tenant.auth.verification.notice'])
    ->group(function (): void {

        Route::as('organization.')
            ->prefix('organization')
            ->group(function (): void {
                Route::get('edit', [OrganizationController::class, 'edit'])
                    ->name('edit');

                Route::put('/', [OrganizationController::class, 'update'])
                    ->name('update');
            });

        Route::as('time-off-types.')
            ->prefix('time-off-types')
            ->group(function (): void {
                Route::get('/', [TimeOffTypesController::class, 'index'])
                    ->name('index');

                Route::get('create', [TimeOffTypesController::class, 'create'])
                    ->name('create');

                Route::post('/', [TimeOffTypesController::class, 'store'])
                    ->name('store');
            });

    });
