<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class LogoutController
{
    public function store(): RedirectResponse
    {
        Auth::logout();

        return to_route('auth.login.index');
    }
}
