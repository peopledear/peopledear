<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

final class UserAvatarController
{
    public function destroy(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->avatar->path) {
            Storage::disk('public')->delete($user->avatar->path);
            $user->update(['avatar' => null]);
        }

        return to_route('profile.index');

    }
}
