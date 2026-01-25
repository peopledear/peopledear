<?php

declare(strict_types=1);

use App\Actions\DeleteUserAvatar;
use App\Enums\Disk;
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
        Storage::fake(Disk::S3Public->value);

        /** @var User $user */
        $user = User::factory()->createQuietly();

        $file = UploadedFile::fake()->image('avatar.jpg');
        $path = $file->storeAs(
            'avatars',
            $user->id.'.'.microtime().'.webp',
            Disk::S3Public->value
        );
        $user->update(['avatar' => $path]);

        Disk::S3Public->storage()
            ->assertExists($path);

        $this->action->handle($user);

        expect($user->refresh()->getRawOriginal('avatar'))->toBeNull();

        Disk::S3Public->storage()->assertMissing($path);
    });

test('does nothing when user has no avatar',
    /**
     * @throws Throwable
     */
    function (): void {
        Storage::fake(Disk::S3Public->value);

        /** @var User $user */
        $user = User::factory()->createQuietly(['avatar' => null]);

        $this->action->handle($user);

        expect($user->refresh()->getRawOriginal('avatar'))->toBeNull();
    });
