<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Inertia\Response;

final class UserController
{
    public function index(): Response
    {
        return Inertia::render('users/Index', []);
    }
}
