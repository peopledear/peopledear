<?php

declare(strict_types=1);

use App\Actions\StoreUserAvatar;
use App\Enums\Disk;
use App\Models\User;
use Illuminate\Http\UploadedFile;

beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {
        $this->action = resolve(StoreUserAvatar::class);
    });

test('stores avatar and returns path',
    /**
     * @throws Throwable
     */
    function (): void {

        Disk::S3Public->fake();

        /** @var User $user */
        $user = User::factory()->createQuietly();

        $file = UploadedFile::fake()->image('avatar.jpg', 800, 600);

        $path = $this->action->handle($user, $file);

        $expectedPattern = sprintf('/^avatars\/%s\.\d+\.webp$/', $user->id);

        expect($path)
            ->toMatch($expectedPattern)
            ->and($user->refresh()->getRawOriginal('avatar'))
            ->toBe($path);

        Disk::S3Public->storage()->assertExists($path);
    });

test('resizes large images to max 400x400',
    /**
     * @throws Throwable
     */
    function (): void {

        Disk::S3Public->fake();

        /** @var User $user */
        $user = User::factory()->createQuietly();

        $file = UploadedFile::fake()
            ->image('large.jpg', 2000, 1500);

        $path = $this->action->handle($user, $file);

        expect($path)->not->toBeNull();

        Disk::S3Public->storage()->assertExists($path);
    });

test('deletes old avatar when replacing',
    /**
     * @throws Throwable
     */
    function (): void {
        Disk::S3Public->fake();

        $oldPath = 'avatars/old-avatar.webp';
        Disk::S3Public->storage()->put($oldPath, 'old content');

        /** @var User $user */
        $user = User::factory()->createQuietly(['avatar' => $oldPath]);

        $file = UploadedFile::fake()->image('new-avatar.jpg');

        $newPath = $this->action->handle($user, $file);

        Disk::S3Public->storage()->assertMissing($oldPath);
        Disk::S3Public->storage()->assertExists($newPath);

        expect($user->refresh()->getRawOriginal('avatar'))->toBe($newPath);
    });

test('updates user avatar field',
    /**
     * @throws Throwable
     */
    function (): void {

        Disk::S3Public->fake();

        /** @var User $user */
        $user = User::factory()->create(['avatar' => null]);

        expect($user->getRawOriginal('avatar'))->toBeNull();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $path = $this->action->handle($user, $file);

        $expectedPattern = sprintf('/^avatars\/%s\.\d+\.webp$/', $user->id);

        expect($path)
            ->toMatch($expectedPattern)
            ->and($user->refresh()->getRawOriginal('avatar'))
            ->toBe($path);
    });
