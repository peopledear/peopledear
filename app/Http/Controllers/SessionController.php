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

final readonly class SessionController
{
    public function create(Request $request): Response
    {
        return Inertia::render('session/create', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

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

            return to_route('tenant.auth.two-factor.login', [
                'tenant' => $organization->identifier,
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('tenant.org.overview', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('tenant.auth.login'));
    }
}
