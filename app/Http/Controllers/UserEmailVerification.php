<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

final readonly class UserEmailVerification
{
    public function update(EmailVerificationRequest $request, #[CurrentUser] User $user): RedirectResponse
    {

        if ($user->hasVerifiedEmail()) {
            if (str_starts_with($request->route()->getName(), 'tenant.')) {
                return redirect(route('tenant.org.overview', ['tenant' => $request->route('tenant')]).'?verified=1');
            }

            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        $request->fulfill();

        if (str_starts_with($request->route()->getName(), 'tenant.')) {
            return redirect(route('tenant.org.overview', ['tenant' => $request->route('tenant')]).'?verified=1');
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
