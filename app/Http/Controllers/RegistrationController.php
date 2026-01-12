<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\NewAccountRegistration;
use App\Data\PeopleDear\CreateRegistrationData;
use App\Http\Requests\CreateRegistrationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

use function redirect;
use function route;

final class RegistrationController
{
    public function create(): Response
    {

        return Inertia::render('register/create');

    }

    public function store(CreateRegistrationRequest $request, NewAccountRegistration $action): RedirectResponse
    {

        $user = $action->handle(
            data: CreateRegistrationData::from($request->safe())
        );

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
