<?php

declare(strict_types=1);

use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified:tenant.auth.verification.notice'])->group(function (): void {
    Route::prefix('org')->group(static function (): void {

        Route::get('/', [OrganizationController::class, 'index'])
            ->name('overview');

    });
});
