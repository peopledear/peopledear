<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\User\CreateUserEmailVerificationNotification;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class UserEmailVerificationNotificationController
{
    public function create(
        Request $request,
        #[CurrentUser] User $user,
    ): Response|RedirectResponse {

        if ($user->hasVerifiedEmail()) {
            if (str_starts_with((string) $request->route()->getName(), 'tenant.')) {
                return redirect(route('tenant.org.overview', ['tenant' => $request->route('tenant')]));
            }

            return redirect()->intended(route('dashboard', absolute: false));
        }

        return Inertia::render('user-email-verification-notification/create', ['status' => $request->session()->get('status')]);
    }

    public function store(Request $request, #[CurrentUser] User $user, CreateUserEmailVerificationNotification $action): RedirectResponse
    {

        if ($user->hasVerifiedEmail()) {
            if (str_starts_with((string) $request->route()->getName(), 'tenant.')) {
                return redirect(route('tenant.org.overview', ['tenant' => $request->route('tenant')]));
            }

            return redirect()->intended(route('dashboard', absolute: false));
        }

        $action->handle($user);

        return back()->with('status', 'verification-link-sent');
    }
}
