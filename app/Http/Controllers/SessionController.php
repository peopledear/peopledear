<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateSessionRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Sprout\Attributes\CurrentTenant;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;
use function redirect;

final readonly class SessionController
{
    public function create(Request $request): Response
    {
        return Inertia::render('session/create', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * @throws MisconfigurationException
     */
    public function store(
        CreateSessionRequest $request,
        #[CurrentTenant] Organization $organization,
    ): RedirectResponse {
        $user = $request->validateCredentials();

        if ($user->hasEnabledTwoFactorAuthentication()) {
            $request->session()->put([
                'login.id' => $user->getKey(),
                'login.remember' => $request->boolean('remember'),
            ]);

            return redirect(tenant_route(
                name: 'tenant.auth.two-factor.login',
                tenant: $organization
            ));
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended(tenant_route(
            name: 'tenant.org.overview',
            tenant: $organization,
            absolute: false
        ));
    }

    /**
     * @throws MisconfigurationException
     */
    public function destroy(
        Request $request,
        #[CurrentTenant] Organization $organization,
    ): RedirectResponse {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(tenant_route(
            name: 'tenant.auth.login',
            tenant: $organization
        ));
    }
}
