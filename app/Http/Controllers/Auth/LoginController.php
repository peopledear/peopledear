<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

use function auth;

final class LoginController
{
    public function index(): Response
    {
        return Inertia::render('auth/Login', []);
    }

    public function store(LoginRequest $request): RedirectResponse
    {

        throw_unless(auth()->attempt([
            'email' => $request->string('email')->toString(),
            'password' => $request->string('password')->toString(),
        ], $request->boolean('remember')), ValidationException::withMessages([
            'email' => 'Invalid Credentials',
        ]));

        $user = User::query()
            ->where('email', $request->string('email')
                ->toString())->first();

        if ($user) {
            auth()->login($user);
        }

        return redirect('dashboard');

    }
}
