<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

final class UserAvatarController
{
    public function destroy(#[CurrentUser] User $user): RedirectResponse
    {
        if ($user->avatar->path) {
            Storage::disk('public')
                ->delete($user->avatar->path);

            $user->update(['avatar' => null]);
        }

        return to_route('profile.index');
    }
}
