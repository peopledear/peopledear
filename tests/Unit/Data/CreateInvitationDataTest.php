<?php

declare(strict_types=1);

use App\Data\CreateInvitationData;
use App\Models\Role;
use Illuminate\Validation\ValidationException;

test('it validates required email', function () {
    $role = Role::query()->where('name', 'employee')->first();
    $data = ['role_id' => $role->id];

    CreateInvitationData::validateAndCreate($data);

})->throws(ValidationException::class, 'email');

test('it validates email format', function () {
    $role = Role::query()->where('name', 'employee')->first();
    $data = [
        'email' => 'invalid-email',
        'role_id' => $role->id,
    ];

    CreateInvitationData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it validates email max length', function () {
    $role = Role::query()->where('name', 'employee')->first();
    $data = [
        'email' => str_repeat('a', 256).'@example.com',
        'role_id' => $role->id,
    ];

    CreateInvitationData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it validates required role_id', function () {
    $data = ['email' => 'test@example.com'];

    CreateInvitationData::validateAndCreate($data);
})->throws(ValidationException::class, 'role id');

test('it validates role_id exists', function () {
    $data = [
        'email' => 'test@example.com',
        'role_id' => 99999,
    ];

    CreateInvitationData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it creates data object with valid data', function () {
    $role = Role::query()->where('name', 'employee')->first();
    $data = [
        'email' => 'test@example.com',
        'role_id' => $role->id,
    ];

    $result = CreateInvitationData::validateAndCreate($data);

    expect($result)->toBeInstanceOf(CreateInvitationData::class)
        ->and($result->email)->toBe('test@example.com')
        ->and($result->role_id)->toBe($role->id);
});

test('it skips validation when using from method', function () {
    $data = [
        'email' => 'invalid-email',
        'role_id' => 99999,
    ];

    $result = CreateInvitationData::from($data);

    expect($result)->toBeInstanceOf(CreateInvitationData::class)
        ->and($result->email)->toBe('invalid-email')
        ->and($result->role_id)->toBe(99999);
});
