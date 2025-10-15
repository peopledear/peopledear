<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Requests\UpdateUserProfileRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

use function to_route;

final class UserProfileController
{
    public function index(Request $request): Response
    {
        return Inertia::render('profile/General', [
            'user' => $request->user()?->only([
                'id',
                'name',
                'email',
                'avatar',
            ])]);
    }

    public function update(UpdateUserProfileRequest $request): RedirectResponse
    {

        $validated = $request->validated();

        /** @var User $user */
        $user = $request->user();

        if ($request->hasFile('avatar')) {

            if ($user->avatar->path) {
                Storage::disk('public')->delete($user->avatar->path);
            }

            // store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');

            $validated['avatar'] = $path;

        }

        $user->update($validated);

        return to_route('profile.index')
            ->with('success', __('Profile updated successfully'));

    }
}
