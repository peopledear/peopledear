<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\User\DeleteUser;
use App\Http\Requests\DeleteUserRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Sprout\Attributes\CurrentTenant;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

final readonly class UserController
{
    /**
     * @throws MisconfigurationException
     */
    public function destroy(
        DeleteUserRequest $request,
        #[CurrentUser] User $user,
        #[CurrentTenant] Organization $organization,
        DeleteUser $action
    ): RedirectResponse {
        Auth::logout();

        $action->handle($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(tenant_route('tenant.auth.login', $organization));
    }
}
