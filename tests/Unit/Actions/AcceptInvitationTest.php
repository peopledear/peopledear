<?php

declare(strict_types=1);

use App\Actions\AcceptInvitation;
use App\Models\Invitation;
use Carbon\CarbonInterface;

test('it sets accepted_at timestamp', function () {
    $invitation = Invitation::factory()->pending()->create();
    $action = new AcceptInvitation();

    expect($invitation->accepted_at)->toBeNull();

    $result = $action->handle($invitation);

    expect($result->accepted_at)->toBeInstanceOf(CarbonInterface::class)
        ->and($result->accepted_at)->not->toBeNull();
});

test('it returns the updated invitation', function () {
    $invitation = Invitation::factory()->pending()->create();
    $action = new AcceptInvitation();

    $result = $action->handle($invitation);

    expect($result)->toBeInstanceOf(Invitation::class)
        ->and($result->id)->toBe($invitation->id)
        ->and($result->isAccepted())->toBeTrue();
});

test('it works with pending invitations', function () {
    $invitation = Invitation::factory()->pending()->create();
    $action = new AcceptInvitation();

    expect($invitation->isPending())->toBeTrue();

    $result = $action->handle($invitation);

    expect($result->isPending())->toBeFalse()
        ->and($result->isAccepted())->toBeTrue();
});
