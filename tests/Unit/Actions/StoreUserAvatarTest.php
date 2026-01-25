<?php

declare(strict_types=1);

use App\Actions\StoreUserAvatar;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->createQuietly();

        $file = UploadedFile::fake()->image('avatar.jpg', 800, 600);

        $path = $this->action->handle($user, $file);

        $expectedPattern = sprintf('/^avatars\/%s\.\d+\.webp$/', $user->id);

        expect($path)
            ->toMatch($expectedPattern)
            ->and($user->refresh()->getRawOriginal('avatar'))
            ->toBe($path);

        Storage::disk('public')->assertExists($path);
    });

test('deletes old avatar when storing new one',
    /**
     * @throws Throwable
     */
    function (): void {
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->createQuietly();

        // Store an old avatar first
        $oldFile = UploadedFile::fake()->image('old.jpg');
        $oldPath = $oldFile->storeAs('avatars', $user->id.'.webp', 'public');
        $user->update(['avatar' => $oldPath]);

        Storage::disk('public')->assertExists($oldPath);

        // Store a new avatar
        $newFile = UploadedFile::fake()->image('new.jpg');
        $path = $this->action->handle($user, $newFile);

        $expectedPattern = sprintf('/^avatars\/%s\.\d+\.webp$/', $user->id);

        expect($path)
            ->toMatch($expectedPattern)
            ->and($path)
            ->not->toBe($oldPath);

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($path);
    });

test('resizes large images to max 400x400',
    /**
     * @throws Throwable
     */
    function (): void {
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->createQuietly();

        $file = UploadedFile::fake()->image('large.jpg', 2000, 1500);

        $path = $this->action->handle($user, $file);

        expect($path)->not->toBeNull();

        Storage::disk('public')->assertExists($path);
    });

test('updates user avatar field',
    /**
     * @throws Throwable
     */
    function (): void {
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->createQuietly(['avatar' => null]);

        expect($user->getRawOriginal('avatar'))->toBeNull();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $path = $this->action->handle($user, $file);

        $expectedPattern = sprintf('/^avatars\/%s\.\d+\.webp$/', $user->id);

        expect($path)
            ->toMatch($expectedPattern)
            ->and($user->refresh()->getRawOriginal('avatar'))
            ->toBe($path);
    });
