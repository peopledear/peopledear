<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\ResendInvitation;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;

use function to_route;

final class ResendInvitationController
{
    public function __invoke(
        Invitation $invitation,
        ResendInvitation $resendInvitation
    ): RedirectResponse {
        if ($invitation->isAccepted()) {
            return to_route('admin.users.index')
                ->withErrors([
                    'invitation' => __('This invitation has already been accepted.'),
                ]);
        }

        $resendInvitation->handle($invitation);

        return to_route('admin.users.index')
            ->with('success', __('Invitation resent successfully'));
    }
}
