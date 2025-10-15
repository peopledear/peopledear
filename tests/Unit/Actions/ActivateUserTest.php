<?php

declare(strict_types=1);

use App\Actions\ActivateUser;
use App\Models\User;

test('it sets is_active to true', function () {
    $user = User::factory()->inactive()->create();
    $action = new ActivateUser();

    expect($user->is_active)->toBeFalse();

    $result = $action->handle($user);

    expect($result->is_active)->toBeTrue();
});

test('it returns the updated user', function () {
    $user = User::factory()->inactive()->create();
    $action = new ActivateUser();

    $result = $action->handle($user);

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->id)->toBe($user->id)
        ->and($result->is_active)->toBeTrue();
});

test('it works with inactive users', function () {
    $user = User::factory()->inactive()->create();
    $action = new ActivateUser();

    expect($user->is_active)->toBeFalse();

    $result = $action->handle($user);

    expect($result->is_active)->toBeTrue();
});
