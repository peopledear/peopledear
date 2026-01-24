<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Organization\RegisterOrganization;
use App\Data\PeopleDear\CreateRegistrationData;
use App\Http\Requests\CreateRegistrationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

use function App\tenant_route;
use function redirect;

final class RegistrationController
{
    public function create(): Response
    {

        return Inertia::render('register/create');

    }

    /**
     * @throws Throwable
     */
    public function store(
        CreateRegistrationRequest $request,
        RegisterOrganization $action
    ): RedirectResponse {

        $user = $action->handle(
            data: CreateRegistrationData::from($request->safe())
        );

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended(tenant_route('tenant.org.overview', $user->organization));
    }
}
