<?php

declare(strict_types=1);

use App\Data\UpdateUserRoleData;
use App\Models\Role;
use Illuminate\Validation\ValidationException;

test('it validates required role_id', function () {
    $data = [];

    UpdateUserRoleData::validateAndCreate($data);
})->throws(ValidationException::class, 'role id');

test('it validates role_id exists', function () {
    $data = ['role_id' => 99999];

    UpdateUserRoleData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it creates data object with valid data', function () {
    $role = Role::query()->where('name', 'manager')->first();
    $data = ['role_id' => $role->id];

    $result = UpdateUserRoleData::validateAndCreate($data);

    expect($result)->toBeInstanceOf(UpdateUserRoleData::class)
        ->and($result->role_id)->toBe($role->id);
});

test('it skips validation when using from method', function () {
    $data = ['role_id' => 99999];

    $result = UpdateUserRoleData::from($data);

    expect($result)->toBeInstanceOf(UpdateUserRoleData::class)
        ->and($result->role_id)->toBe(99999);
});
