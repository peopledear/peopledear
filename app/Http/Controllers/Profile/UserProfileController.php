<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Data\UpdateUserProfileData;
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
            'user' => $request
                ->user()
                ?->only([
                    'id',
                    'name',
                    'email',
                    'avatar',
                ]),
        ]);
    }

    public function update(UpdateUserProfileData $data, Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $updateData = [
            'name' => $data->name,
            'email' => $data->email,
        ];

        if ($data->avatar !== null) {
            if ($user->avatar->path) {
                Storage::disk('public')
                    ->delete($user->avatar->path);
            }

            $path = $data->avatar->store('avatars', 'public');
            $updateData['avatar'] = $path;
        }

        $user->update($updateData);

        return to_route('profile.index')
            ->with('success', __('Profile updated successfully'));
    }
}
