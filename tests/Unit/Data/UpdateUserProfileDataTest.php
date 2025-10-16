<?php

declare(strict_types=1);

use App\Data\UpdateUserProfileData;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('it validates required name', function () {
    $data = ['email' => 'test@example.com'];

    UpdateUserProfileData::validateAndCreate($data);
})->throws(ValidationException::class, 'name');

test('it validates name is a string', function () {
    $data = [
        'name' => 12345,
        'email' => 'test@example.com',
    ];

    UpdateUserProfileData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it validates name max length', function () {
    $data = [
        'name' => str_repeat('a', 256),
        'email' => 'test@example.com',
    ];

    UpdateUserProfileData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it validates required email', function () {
    $data = ['name' => 'John Doe'];

    UpdateUserProfileData::validateAndCreate($data);
})->throws(ValidationException::class, 'email');

test('it validates email format', function () {
    $data = [
        'name' => 'John Doe',
        'email' => 'invalid-email',
    ];

    UpdateUserProfileData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it validates email max length', function () {
    $data = [
        'name' => 'John Doe',
        'email' => str_repeat('a', 256).'@example.com',
    ];

    UpdateUserProfileData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it validates email uniqueness', function () {
    $otherUser = User::factory()->create(['email' => 'existing@example.com']);

    $data = [
        'name' => 'John Doe',
        'email' => 'existing@example.com',
    ];

    UpdateUserProfileData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it allows current user email', function () {
    $data = [
        'name' => 'John Doe',
        'email' => $this->user->email,
    ];

    $result = UpdateUserProfileData::validateAndCreate($data);

    expect($result)->toBeInstanceOf(UpdateUserProfileData::class)
        ->and($result->name)->toBe('John Doe')
        ->and($result->email)->toBe($this->user->email);
});

test('it validates avatar is nullable', function () {
    $data = [
        'name' => 'John Doe',
        'email' => 'test@example.com',
    ];

    $result = UpdateUserProfileData::from($data);

    expect($result->avatar)->toBeNull();
});

test('it validates avatar is an image', function () {
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $data = [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'avatar' => $file,
    ];

    UpdateUserProfileData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it validates avatar max size', function () {
    $file = UploadedFile::fake()->image('avatar.jpg')->size(3000);

    $data = [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'avatar' => $file,
    ];

    UpdateUserProfileData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it creates data object with valid data', function () {
    $data = [
        'name' => 'John Doe',
        'email' => 'newemail@example.com',
    ];

    $result = UpdateUserProfileData::from($data);

    expect($result)->toBeInstanceOf(UpdateUserProfileData::class)
        ->and($result->name)->toBe('John Doe')
        ->and($result->email)->toBe('newemail@example.com')
        ->and($result->avatar)->toBeNull();
});

test('it creates data object with avatar', function () {
    $file = UploadedFile::fake()->image('avatar.jpg', 500, 500)->size(1000);

    $data = [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'avatar' => $file,
    ];

    $result = UpdateUserProfileData::from($data);

    expect($result)->toBeInstanceOf(UpdateUserProfileData::class)
        ->and($result->avatar)->toBeInstanceOf(UploadedFile::class);
});

test('it skips validation when using from method', function () {
    $data = [
        'name' => 'John Doe',
        'email' => 'invalid-email',
    ];

    $result = UpdateUserProfileData::from($data);

    expect($result)->toBeInstanceOf(UpdateUserProfileData::class)
        ->and($result->name)->toBe('John Doe')
        ->and($result->email)->toBe('invalid-email');
});
