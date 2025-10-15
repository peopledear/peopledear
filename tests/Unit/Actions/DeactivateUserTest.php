<?php

declare(strict_types=1);

use App\Actions\DeactivateUser;
use App\Models\User;

test('it sets is_active to false', function () {
    $user = User::factory()->create(['is_active' => true]);
    $action = new DeactivateUser();

    expect($user->is_active)->toBeTrue();

    $result = $action->handle($user);

    expect($result->is_active)->toBeFalse();
});

test('it returns the updated user', function () {
    $user = User::factory()->create(['is_active' => true]);
    $action = new DeactivateUser();

    $result = $action->handle($user);

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->id)->toBe($user->id)
        ->and($result->is_active)->toBeFalse();
});

test('it works with active users', function () {
    $user = User::factory()->create(['is_active' => true]);
    $action = new DeactivateUser();

    expect($user->is_active)->toBeTrue();

    $result = $action->handle($user);

    expect($result->is_active)->toBeFalse();
});
