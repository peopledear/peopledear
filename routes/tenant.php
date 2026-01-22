<?php

declare(strict_types=1);

use App\Models\Organization;
use Illuminate\Support\Facades\Route;
use Sprout\Attributes\CurrentTenant;

Route::get('/', fn (
    #[CurrentTenant] Organization $organization
): string => 'Welcome to the '.$organization->name.' tenant!')
    ->name('welcome');

Route::as('org.')
    ->group(__DIR__.'/tenant/organization.php');

Route::as('employee.')
    ->group(__DIR__.'/tenant/employee.php');

Route::as('settings.')
    ->prefix('settings')
    ->group(__DIR__.'/tenant/settings.php');

Route::as('auth.')
    ->group(__DIR__.'/tenant/auth.php');

Route::as('user.')
    ->group(__DIR__.'/tenant/user.php');

Route::as('notifications.')
    ->group(__DIR__.'/tenant/notifications.php');
