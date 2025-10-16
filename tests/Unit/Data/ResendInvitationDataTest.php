<?php

declare(strict_types=1);

use App\Data\ResendInvitationData;
use Illuminate\Validation\ValidationException;

test('it validates required email', function () {
    $data = [];

    ResendInvitationData::validateAndCreate($data);
})->throws(ValidationException::class, 'email');

test('it validates email format', function () {
    $data = ['email' => 'invalid-email'];

    ResendInvitationData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it validates email max length', function () {
    $data = ['email' => str_repeat('a', 256).'@example.com'];

    ResendInvitationData::validateAndCreate($data);
})->throws(ValidationException::class);

test('it creates data object with valid data', function () {
    $data = ['email' => 'test@example.com'];

    $result = ResendInvitationData::validateAndCreate($data);

    expect($result)->toBeInstanceOf(ResendInvitationData::class)
        ->and($result->email)->toBe('test@example.com');
});

test('it skips validation when using from method', function () {
    $data = ['email' => 'invalid-email'];

    $result = ResendInvitationData::from($data);

    expect($result)->toBeInstanceOf(ResendInvitationData::class)
        ->and($result->email)->toBe('invalid-email');
});
