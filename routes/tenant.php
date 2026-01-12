<?php

declare(strict_types=1);

use App\Models\Organization;
use Illuminate\Support\Facades\Route;
use Sprout\Attributes\CurrentTenant;

Route::get('/', fn (
    #[CurrentTenant] Organization $organization
): string => 'Welcome to the '.$organization->name.' tenant!');

Route::as('settings.')
    ->prefix('settings')
    ->group(__DIR__.'/tenant/settings.php');
