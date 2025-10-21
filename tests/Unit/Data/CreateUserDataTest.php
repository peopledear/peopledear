<?php

declare(strict_types=1);

use App\Data\CreateUserData;
use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\ValidationException;

test('it validates required name', function () {
    $role = Role::query()->where('name', 'employee')->first();
    $data = [
        'email' => 'test@example.com',
        'password' => 'password123',
        'role_id' => $role->id,
    ];

    CreateUserData::validateAndCreate($data);
})->throws(ValidationException::class, 'name');

test('it validates name max length', function () {
    $role = Role::query()->where('name', 'employee')->first();
    $data = [
        'name' => str_repeat('a', 256),
        'email' => 'test@example.com',
        'password' => 'password123',
        'role_id' => $role->id,
    ];

    CreateUserData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it validates required email', function () {
    $role = Role::query()->where('name', 'employee')->first();
    $data = [
        'name' => 'Test User',
        'password' => 'password123',
        'role_id' => $role->id,
    ];

    CreateUserData::validateAndCreate($data);
})->throws(ValidationException::class, 'email');

test('it validates email format', function () {
    $role = Role::query()->where('name', 'employee')->first();
    $data = [
        'name' => 'Test User',
        'email' => 'invalid-email',
        'password' => 'password123',
        'role_id' => $role->id,
    ];

    CreateUserData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it validates email max length', function () {
    $role = Role::query()->where('name', 'employee')->first();
    $data = [
        'name' => 'Test User',
        'email' => str_repeat('a', 256).'@example.com',
        'password' => 'password123',
        'role_id' => $role->id,
    ];

    CreateUserData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it validates email uniqueness', function () {
    $role = Role::query()->where('name', 'employee')->first();
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);

    $data = [
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'role_id' => $role->id,
    ];

    CreateUserData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it validates required password', function () {
    $role = Role::query()->where('name', 'employee')->first();
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role_id' => $role->id,
    ];

    CreateUserData::validateAndCreate($data);
})->throws(ValidationException::class, 'password');

test('it validates password minimum length', function () {
    $role = Role::query()->where('name', 'employee')->first();
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'short',
        'role_id' => $role->id,
    ];

    CreateUserData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it validates required role_id', function () {
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ];

    CreateUserData::validateAndCreate($data);
})->throws(ValidationException::class, 'role id');

test('it validates role_id exists', function () {
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'role_id' => 99999,
    ];

    CreateUserData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it creates data object with valid data', function () {
    $role = Role::query()->where('name', 'employee')->first();
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'role_id' => $role->id,
    ];

    $result = CreateUserData::validateAndCreate($data);

    expect($result)->toBeInstanceOf(CreateUserData::class)
        ->and($result->name)->toBe('Test User')
        ->and($result->email)->toBe('test@example.com')
        ->and($result->password)->toBe('password123')
        ->and($result->role_id)->toBe($role->id);
});

test('it skips validation when using from method', function () {
    $data = [
        'name' => 'Test User',
        'email' => 'invalid-email',
        'password' => 'short',
        'role_id' => 99999,
    ];

    $result = CreateUserData::from($data);

    expect($result)->toBeInstanceOf(CreateUserData::class)
        ->and($result->name)->toBe('Test User')
        ->and($result->email)->toBe('invalid-email')
        ->and($result->password)->toBe('short')
        ->and($result->role_id)->toBe(99999);
});
