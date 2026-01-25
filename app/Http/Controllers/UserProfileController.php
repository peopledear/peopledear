<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\User\UpdateUser;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

final readonly class UserProfileController
{
    public function edit(): Response
    {
        return Inertia::render('user-profile/edit', [
            'status' => session('status'),
        ]);
    }

    /**
     * @throws MisconfigurationException
     */
    public function update(UpdateUserRequest $request, #[CurrentUser] User $user, UpdateUser $action): RedirectResponse
    {
        $action->handle($user, $request->validated());

        return redirect(tenant_route(
            name: 'tenant.user.settings.profile.edit',
            tenant: $user->organization
        ));
    }
}
