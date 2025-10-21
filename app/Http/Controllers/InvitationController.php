<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateInvitation;
use App\Data\CreateInvitationData;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;

use function to_route;

final class InvitationController
{
    public function store(
        CreateInvitationData $data,
        CreateInvitation $createInvitation,
        #[CurrentUser] User $user
    ): RedirectResponse {
        $createInvitation->handle(
            email: $data->email,
            roleId: $data->role_id,
            invitedBy: $user->id
        );

        return to_route('admin.users.index')
            ->with('success', __('Invitation sent successfully'));
    }

    public function destroy(Invitation $invitation): RedirectResponse
    {
        $invitation->delete();

        return to_route('admin.users.index')
            ->with('success', __('Invitation deleted successfully'));
    }
}
