<?php

declare(strict_types=1);

use App\Http\Controllers\LocationController;
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

        Route::as('locations.')
            ->prefix('locations')
            ->group(function (): void {
                Route::post('/', [LocationController::class, 'store'])
                    ->name('store');

                Route::put('{location}', [LocationController::class, 'update'])
                    ->name('update');

                Route::delete('{location}', [LocationController::class, 'destroy'])
                    ->name('destroy');
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
