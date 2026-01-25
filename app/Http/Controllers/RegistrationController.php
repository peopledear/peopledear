<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\GenerateCrossDomainToken;
use App\Actions\Organization\RegisterOrganization;
use App\Data\PeopleDear\CreateRegistrationData;
use App\Http\Requests\CreateRegistrationRequest;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function App\tenant_route;

final class RegistrationController
{
    public function create(): InertiaResponse
    {
        return Inertia::render('register/create');
    }

    /**
     * @throws Throwable
     */
    public function store(
        CreateRegistrationRequest $request,
        RegisterOrganization $action,
        GenerateCrossDomainToken $tokenAction
    ): Response {
        $user = $action->handle(
            data: CreateRegistrationData::from($request->safe())
        );

        $intended = tenant_route('tenant.org.overview', $user->organization);
        $token = $tokenAction->handle($user, $user->organization, $intended);

        $authUrl = tenant_route(
            'tenant.auth.cross-domain',
            $user->organization,
            ['nonce' => $token->nonce]
        );

        return Inertia::location($authUrl);
    }
}
