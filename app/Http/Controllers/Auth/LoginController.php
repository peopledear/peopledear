<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Inertia\Response;

final class LoginController
{

    public function index(): Response
    {
        return Inertia::render('Auth/Login');
    }


}
