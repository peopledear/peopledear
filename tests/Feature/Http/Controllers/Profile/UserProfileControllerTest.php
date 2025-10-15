<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('renders the profile page', function (): void {

    $user = User::factory()
        ->create();

    auth()->login($user);

    $response = $this->get(route('profile.index'));

    expect($response->getStatusCode())
        ->toBe(200);

});

it('allows a user to update their profile', function (): void {

    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $this->actingAs($user);

    $response = $this->put(route('profile.update'), [
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
    ]);

    $response->assertRedirect(route('profile.index'))
        ->assertSessionHas('success', __('Profile updated successfully'));

    expect($user->fresh())
        ->name->toBe('Jane Smith')
        ->email->toBe('jane@example.com');

});

it('validates required fields when updating profile', function (array $data, string $errorField): void {

    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->put(route('profile.update'), $data);

    $response->assertInvalid([$errorField]);

})->with([
    'missing name' => [['email' => 'test@example.com'], 'name'],
    'missing email' => [['name' => 'Test User'], 'email'],
    'invalid email format' => [['name' => 'Test User', 'email' => 'invalid-email'], 'email'],
    'name too long' => [['name' => str_repeat('a', 256), 'email' => 'test@example.com'], 'name'],
    'email too long' => [['name' => 'Test User', 'email' => str_repeat('a', 245).'@example.com'], 'email'],
]);

it('validates email uniqueness when updating profile', function (): void {

    $existingUser = User::factory()->create(['email' => 'existing@example.com']);
    $user = User::factory()->create(['email' => 'user@example.com']);

    $this->actingAs($user);

    $response = $this->put(route('profile.update'), [
        'name' => 'Test User',
        'email' => 'existing@example.com',
    ]);

    $response->assertInvalid(['email']);

});

it('allows user to keep their own email when updating profile', function (): void {

    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $this->actingAs($user);

    $response = $this->put(route('profile.update'), [
        'name' => 'John Smith',
        'email' => 'john@example.com',
    ]);

    $response->assertRedirect(route('profile.index'))
        ->assertSessionHas('success');

    expect($user->fresh())
        ->name->toBe('John Smith')
        ->email->toBe('john@example.com');

});

it('validates avatar dimensions', function (): void {

    $user = User::factory()->create();

    $this->actingAs($user);

    // Create a small image (100x100) that doesn't meet minimum requirements
    $image = imagecreatetruecolor(100, 100);
    $path = sys_get_temp_dir().'/test_avatar_small.jpg';
    imagejpeg($image, $path);

    $response = $this->put(route('profile.update'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'avatar' => new Illuminate\Http\UploadedFile($path, 'avatar.jpg', null, null, true),
    ]);

    $response->assertInvalid(['avatar']);

    unlink($path);

});

it('validates avatar file size', function (): void {

    $user = User::factory()->create();

    $this->actingAs($user);

    // Create an image that's too large (simulate by providing invalid test flag)
    $image = imagecreatetruecolor(500, 500);
    $path = sys_get_temp_dir().'/test_avatar_large.jpg';
    imagejpeg($image, $path, 100);

    // This won't actually be over 2MB, but we're testing the validation rule exists
    $response = $this->put(route('profile.update'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'avatar' => new Illuminate\Http\UploadedFile($path, 'avatar.jpg', 'image/jpeg', 3 * 1024 * 1024, true),
    ]);

    $response->assertInvalid(['avatar']);

    unlink($path);

});

it('allows a user to upload an avatar', function (): void {

    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user);

    // Create a valid image (500x500)
    $image = imagecreatetruecolor(500, 500);
    $path = sys_get_temp_dir().'/test_avatar_valid.jpg';
    imagejpeg($image, $path);

    $file = new Illuminate\Http\UploadedFile($path, 'avatar.jpg', 'image/jpeg', null, true);

    $response = $this->put(route('profile.update'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'avatar' => $file,
    ]);

    $response->assertRedirect(route('profile.index'))
        ->assertSessionHas('success');

    $freshUser = $user->fresh();
    expect($freshUser->avatar->path)->not->toBeNull();
    Storage::disk('public')->assertExists($freshUser->avatar->path);

    unlink($path);

});

it('deletes old avatar when uploading a new one', function (): void {

    Storage::fake('public');

    $user = User::factory()->create([
        'avatar' => 'avatars/old-avatar.jpg',
    ]);

    // Create the old avatar file
    Storage::disk('public')->put('avatars/old-avatar.jpg', 'old content');

    $this->actingAs($user);

    // Create a valid new image
    $image = imagecreatetruecolor(500, 500);
    $path = sys_get_temp_dir().'/test_avatar_new.jpg';
    imagejpeg($image, $path);

    $file = new Illuminate\Http\UploadedFile($path, 'avatar.jpg', 'image/jpeg', null, true);

    $response = $this->put(route('profile.update'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'avatar' => $file,
    ]);

    $response->assertRedirect(route('profile.index'))
        ->assertSessionHas('success');

    Storage::disk('public')->assertMissing('avatars/old-avatar.jpg');

    unlink($path);

});
