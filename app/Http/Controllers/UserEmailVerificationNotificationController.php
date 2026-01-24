<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\User\CreateUserEmailVerificationNotification;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

use function App\tenant_route;

final readonly class UserEmailVerificationNotificationController
{
    public function create(
        #[CurrentUser] User $user,
    ): Response|RedirectResponse {
        if ($user->hasVerifiedEmail()) {
            return redirect(tenant_route('tenant.org.overview', $user->organization));
        }

        return Inertia::render('user-email-verification-notification/create', ['status' => session('status')]);
    }

    public function store(#[CurrentUser] User $user, CreateUserEmailVerificationNotification $action): RedirectResponse
    {
        if ($user->hasVerifiedEmail()) {
            return redirect(tenant_route('tenant.org.overview', $user->organization));
        }

        $action->handle($user);

        return back()->with('status', 'verification-link-sent');
    }
}
