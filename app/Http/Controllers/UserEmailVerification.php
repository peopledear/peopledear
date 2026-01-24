<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

use function App\tenant_route;

final readonly class UserEmailVerification
{
    public function update(EmailVerificationRequest $request, #[CurrentUser] User $user): RedirectResponse
    {
        if (! $user->hasVerifiedEmail()) {
            $request->fulfill();
        }

        return redirect(tenant_route('tenant.org.overview', $user->organization).'?verified=1');
    }
}
