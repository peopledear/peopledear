<?php

declare(strict_types=1);

use App\Actions\AcceptInvitation;
use App\Actions\CreateUser;
use App\Models\Invitation;
use App\Models\User;
use Carbon\CarbonInterface;

test('it sets accepted_at timestamp', function () {
    $invitation = Invitation::factory()->pending()->create();
    $action = new AcceptInvitation(new CreateUser());

    expect($invitation->accepted_at)->toBeNull();

    $user = $action->handle($invitation, 'Test User', 'password123');

    $invitation->refresh();

    expect($invitation->accepted_at)->toBeInstanceOf(CarbonInterface::class)
        ->and($invitation->accepted_at)->not->toBeNull()
        ->and($user)->toBeInstanceOf(User::class);
});

test('it returns the created user', function () {
    $invitation = Invitation::factory()->pending()->create();
    $action = new AcceptInvitation(new CreateUser());

    $user = $action->handle($invitation, 'Test User', 'password123');

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Test User')
        ->and($user->email)->toBe($invitation->email);
});

test('it works with pending invitations', function () {
    $invitation = Invitation::factory()->pending()->create();
    $action = new AcceptInvitation(new CreateUser());

    expect($invitation->isPending())->toBeTrue();

    $action->handle($invitation, 'Test User', 'password123');

    $invitation->refresh();

    expect($invitation->isPending())->toBeFalse()
        ->and($invitation->isAccepted())->toBeTrue();
});
