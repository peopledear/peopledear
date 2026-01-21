<?php

declare(strict_types=1);

use App\Http\Controllers\DeleteNotificationController;
use App\Http\Controllers\DropdownNotificationController;
use App\Http\Controllers\MarkAllNotificationsAsReadController;
use App\Http\Controllers\MarkNotificationAsReadController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {

    // Notifications...
    Route::get('dropdown', [DropdownNotificationController::class, 'index'])
        ->name('dropdown');

    Route::post('{notification}/mark-read', [MarkNotificationAsReadController::class, 'store'])
        ->name('mark-read');
    Route::post('mark-all-read', [MarkAllNotificationsAsReadController::class, 'store'])
        ->name('mark-all-read');
    Route::delete('{notification}', [DeleteNotificationController::class, 'destroy'])
        ->name('destroy');
});
