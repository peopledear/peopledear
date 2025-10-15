<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('allows a user to delete their avatar', function (): void {

    Storage::fake('public');

    $user = User::factory()->create([
        'avatar' => 'avatars/user-avatar.jpg',
    ]);

    // Create the avatar file
    Storage::disk('public')->put('avatars/user-avatar.jpg', 'content');

    $this->actingAs($user);

    $response = $this->delete(route('profile.avatar.destroy'));

    $response->assertRedirect(route('profile.index'));

    expect($user->fresh()->avatar->path)->toBeNull();
    Storage::disk('public')->assertMissing('avatars/user-avatar.jpg');

});

it('does nothing when deleting avatar if user has no avatar', function (): void {

    $user = User::factory()->create([
        'avatar' => null,
    ]);

    $this->actingAs($user);

    $response = $this->delete(route('profile.avatar.destroy'));

    $response->assertRedirect(route('profile.index'));

    expect($user->fresh()->avatar->path)->toBeNull();

});

it('requires authentication to delete avatar', function (): void {

    $response = $this->delete(route('profile.avatar.destroy'));

    $response->assertRedirect(route('auth.login.index'));

});
