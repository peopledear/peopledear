<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DeleteUserAvatar;
use App\Actions\StoreUserAvatar;
use App\Http\Requests\StoreUserAvatarRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Sprout\Attributes\CurrentTenant;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

final readonly class UserAvatarController
{
    public function store(
        StoreUserAvatarRequest $request,
        StoreUserAvatar $action,
        #[CurrentUser] User $user,
        #[CurrentTenant] Organization $organization,
    ): RedirectResponse {
        /** @var UploadedFile $file */
        $file = $request->validated('avatar');

        $action->handle($user, $file);

        return redirect(tenant_route(
            name: 'tenant.user.settings.profile.edit',
            tenant: $organization
        ))->with('success', 'Avatar uploaded successfully');
    }

    /**
     * @throws MisconfigurationException
     */
    public function destroy(
        DeleteUserAvatar $action,
        #[CurrentUser] User $user,
        #[CurrentTenant] Organization $organization,
    ): RedirectResponse {
        $action->handle($user);

        return redirect(tenant_route(
            name: 'tenant.user.settings.profile.edit',
            tenant: $organization
        ))->with('success', 'Avatar deleted successfully');
    }
}
