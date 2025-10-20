<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Queries\PendingInvitationsQuery;
use App\Queries\RolesQuery;
use App\Queries\UsersQuery;
use Inertia\Inertia;
use Inertia\Response;

final class UserController
{
    public function index(
        UsersQuery $usersQuery,
        PendingInvitationsQuery $pendingInvitationsQuery,
        RolesQuery $rolesQuery
    ): Response {
        $users = $usersQuery
            ->builder()
            ->paginate(15);

        $pendingInvitations = $pendingInvitationsQuery
            ->builder()
            ->get();

        $roles = $rolesQuery
            ->builder()
            ->get();

        return Inertia::render('settings/Members', [
            'users' => $users,
            'pendingInvitations' => $pendingInvitations,
            'roles' => $roles,
        ]);
    }
}
