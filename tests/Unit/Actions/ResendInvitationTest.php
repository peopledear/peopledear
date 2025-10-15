<?php

declare(strict_types=1);

use App\Actions\ResendInvitation;
use App\Models\Invitation;
use Carbon\CarbonInterface;

test('it generates new token', function () {
    $invitation = Invitation::factory()->pending()->create();
    $originalToken = $invitation->token;
    $action = new ResendInvitation();

    $result = $action->handle($invitation);

    expect($result->token)->not->toBe($originalToken)
        ->and($result->token)->toBeString()
        ->and($result->token)->toHaveLength(32);
});

test('it resets accepted_at to null', function () {
    $invitation = Invitation::factory()->accepted()->create();
    $action = new ResendInvitation();

    expect($invitation->accepted_at)->not->toBeNull();

    $result = $action->handle($invitation);

    expect($result->accepted_at)->toBeNull();
});

test('it extends expiry to 7 days from now', function () {
    $invitation = Invitation::factory()->expired()->create();
    $action = new ResendInvitation();

    $result = $action->handle($invitation);

    expect($result->expires_at)->toBeInstanceOf(CarbonInterface::class);

    $expectedExpiry = now()->addDays(7);
    $diffInSeconds = abs($result->expires_at->diffInSeconds($expectedExpiry));

    expect($diffInSeconds)->toBeLessThan(2);
});

test('it works with expired invitations', function () {
    $invitation = Invitation::factory()->expired()->create();
    $action = new ResendInvitation();

    expect($invitation->isExpired())->toBeTrue();

    $result = $action->handle($invitation);

    expect($result->isExpired())->toBeFalse()
        ->and($result->isPending())->toBeTrue();
});

test('it works with accepted invitations', function () {
    $invitation = Invitation::factory()->accepted()->create();
    $action = new ResendInvitation();

    expect($invitation->isAccepted())->toBeTrue();

    $result = $action->handle($invitation);

    expect($result->isAccepted())->toBeFalse()
        ->and($result->accepted_at)->toBeNull()
        ->and($result->isPending())->toBeTrue();
});
