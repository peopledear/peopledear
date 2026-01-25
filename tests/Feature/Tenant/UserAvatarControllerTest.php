<?php

declare(strict_types=1);

use App\Actions\DeleteUserAvatar;
use App\Enums\Disk;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function App\tenant_route;

test('stores avatar for authenticated user',
    /**
     * @throws Throwable
     */
    function (): void {
        Storage::fake(Disk::S3Public->value);

        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        $file = UploadedFile::fake()
            ->image('avatar.jpg', 200, 200);

        $response = $this->actingAs($user)
            ->post(tenant_route('tenant.user.users.avatar.store', $this->tenant), [
                'avatar' => $file,
            ]);

        $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->assertSessionHas('success', 'Avatar uploaded successfully');

        $user->refresh();

        $storedPath = $user->getRawOriginal('avatar');
        $expectedPattern = sprintf('/^avatars\/%s\.\d+\.webp$/', $user->id);

        expect($storedPath)->toMatch($expectedPattern);

        Disk::S3Public->storage()->assertExists($storedPath);
    });

test('replaces old avatar when uploading new one',
    /**
     * @throws Throwable
     */
    function (): void {
        Storage::fake(Disk::S3Public->value);

        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        // Store an old avatar first
        $oldFile = UploadedFile::fake()->image('old.jpg');
        $oldPath = $oldFile->storeAs('avatars', $user->id.'.'.(time() - 1000).'.webp', Disk::S3Public->value);
        $user->update(['avatar' => $oldPath]);

        Disk::S3Public->storage()->assertExists($oldPath);

        $deleteAvatar = resolve(DeleteUserAvatar::class);
        $deleteAvatar->handle($user);

        $newFile = UploadedFile::fake()
            ->image('new.jpg', 200, 200);

        $response = $this->actingAs($user)
            ->post(tenant_route('tenant.user.users.avatar.store', $this->tenant), [
                'avatar' => $newFile,
            ]);

        $response->assertRedirect();

        $user->refresh();
        $newPath = $user->getRawOriginal('avatar');

        $expectedPattern = sprintf('/^avatars\/%s\.\d+\.webp$/', $user->id);

        expect($newPath)
            ->toMatch($expectedPattern)
            ->and($newPath)
            ->not->toBe($oldPath);

        Disk::S3Public->storage()->assertMissing($oldPath);
        Disk::S3Public->storage()->assertExists($newPath);
    });

test('requires avatar field',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->post(tenant_route('tenant.user.users.avatar.store', $this->tenant), []);

        $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->assertSessionHasErrors('avatar');
    });

test('validates avatar is image',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->post(tenant_route('tenant.user.users.avatar.store', $this->tenant), [
                'avatar' => $file,
            ]);

        $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->assertSessionHasErrors('avatar');
    });

test('validates avatar max size',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        $file = UploadedFile::fake()->image('huge.jpg')->size(3000);

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->post(tenant_route('tenant.user.users.avatar.store', $this->tenant), [
                'avatar' => $file,
            ]);

        $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->assertSessionHasErrors('avatar');
    });

test('validates avatar mime types',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        $file = UploadedFile::fake()->create('file.svg', 100, 'image/svg+xml');

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->post(tenant_route('tenant.user.users.avatar.store', $this->tenant), [
                'avatar' => $file,
            ]);

        $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->assertSessionHasErrors('avatar');
    });

test('requires authentication',
    /**
     * @throws Throwable
     */
    function (): void {
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post(tenant_route('tenant.user.users.avatar.store', $this->tenant), [
            'avatar' => $file,
        ]);

        $response->assertRedirect();
    });

test('destroys avatar for authenticated user',
    /**
     * @throws Throwable
     */
    function (): void {
        Storage::fake(Disk::S3Public->value);

        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        $file = UploadedFile::fake()->image('avatar.jpg');
        $path = $file->storeAs('avatars', $user->id.'.webp', Disk::S3Public->value);
        $user->update(['avatar' => $path]);

        Disk::S3Public->storage()->assertExists($path);

        $response = $this->actingAs($user)
            ->delete(tenant_route('tenant.user.users.avatar.destroy', $this->tenant));

        $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->assertSessionHas('success', 'Avatar deleted successfully');

        expect($user->refresh()->getRawOriginal('avatar'))->toBeNull();

        Storage::disk('public')->assertMissing($path);
    });

test('destroy does nothing when user has no avatar',
    /**
     * @throws Throwable
     */
    function (): void {
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly(['avatar' => null]);

        $response = $this->actingAs($user)
            ->delete(tenant_route('tenant.user.users.avatar.destroy', $this->tenant));

        $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->assertSessionHas('success', 'Avatar deleted successfully');

        expect($user->refresh()->getRawOriginal('avatar'))->toBeNull();
    });

test('destroy requires authentication',
    /**
     * @throws Throwable
     */
    function (): void {
        $response = $this->delete(tenant_route('tenant.user.users.avatar.destroy', $this->tenant));

        $response->assertRedirect();
    });
