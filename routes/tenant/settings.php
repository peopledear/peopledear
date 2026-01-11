<?php

declare(strict_types=1);

use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::as('settings.')
    ->prefix('settings')
    ->group(function () {

        Route::get('/organization/edit', [OrganizationController::class, 'edit'])
            ->name('organization.edit');

        Route::put('organization', [OrganizationController::class, 'update'])
            ->name('organization.update');

    });
