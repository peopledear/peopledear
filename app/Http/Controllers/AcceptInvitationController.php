<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AcceptInvitation;
use App\Data\AcceptInvitationData;
use App\Queries\PendingInvitationByTokenQuery;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class AcceptInvitationController
{
    public function show(
        string $token,
        PendingInvitationByTokenQuery $query
    ): Response {
        $invitation = $query
            ->token($token)
            ->withRole()
            ->builder()
            ->firstOrFail();

        abort_if($invitation->isExpired(), 410, 'This invitation has expired.');

        return Inertia::render('AcceptInvitation', [
            'invitation' => [
                'email' => $invitation->email,
                'role' => $invitation->role->display_name,
                'token' => $invitation->token,
            ],
        ]);
    }

    public function store(
        AcceptInvitationData $data,
        string $token,
        AcceptInvitation $acceptInvitation,
        PendingInvitationByTokenQuery $query
    ): RedirectResponse {
        $invitation = $query
            ->token($token)
            ->builder()
            ->firstOrFail();

        if ($invitation->isExpired()) {
            return to_route('auth.login.index')
                ->withErrors([
                    'token' => __('This invitation has expired.'),
                ]);
        }

        $acceptInvitation->handle(
            invitation: $invitation,
            name: $data->name,
            password: $data->password
        );

        return to_route('dashboard')
            ->with('success', __('Welcome to PeopleDear!'));
    }
}
