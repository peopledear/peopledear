<?php

declare(strict_types=1);

use App\Actions\DeleteUserAvatar;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    $this->action = resolve(DeleteUserAvatar::class);
});

test('deletes avatar file and updates user',
    /**
     * @throws Throwable
     */
    function (): void {
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->createQuietly();

        $file = UploadedFile::fake()->image('avatar.jpg');
        $path = $file->storeAs('avatars', $user->id.'.webp', 'public');
        $user->update(['avatar' => $path]);

        Storage::disk('public')->assertExists($path);

        $this->action->handle($user);

        expect($user->refresh()->getRawOriginal('avatar'))->toBeNull();

        Storage::disk('public')->assertMissing($path);
    });

test('does nothing when user has no avatar',
    /**
     * @throws Throwable
     */
    function (): void {
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->createQuietly(['avatar' => null]);

        $this->action->handle($user);

        expect($user->refresh()->getRawOriginal('avatar'))->toBeNull();
    });
