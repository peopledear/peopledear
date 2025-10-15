<?php

declare(strict_types=1);

use App\Actions\CreateInvitation;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Carbon\CarbonInterface;

test('it creates invitation with correct attributes', function () {
    $role = Role::factory()->create();
    $inviter = User::factory()->create();
    $action = new CreateInvitation();

    $invitation = $action->handle('test@example.com', $role->id, $inviter->id);

    expect($invitation)->toBeInstanceOf(Invitation::class)
        ->and($invitation->email)->toBe('test@example.com')
        ->and($invitation->role_id)->toBe($role->id)
        ->and($invitation->invited_by)->toBe($inviter->id)
        ->and($invitation->token)->toBeString()
        ->and($invitation->token)->toHaveLength(32)
        ->and($invitation->accepted_at)->toBeNull();
});

test('it generates unique token', function () {
    $role = Role::factory()->create();
    $inviter = User::factory()->create();
    $action = new CreateInvitation();

    $invitation1 = $action->handle('test1@example.com', $role->id, $inviter->id);
    $invitation2 = $action->handle('test2@example.com', $role->id, $inviter->id);

    expect($invitation1->token)->not->toBe($invitation2->token);
});

test('it sets expiry to 7 days from now', function () {
    $role = Role::factory()->create();
    $inviter = User::factory()->create();
    $action = new CreateInvitation();

    $invitation = $action->handle('test@example.com', $role->id, $inviter->id);

    expect($invitation->expires_at)->toBeInstanceOf(CarbonInterface::class);

    $expectedExpiry = now()->addDays(7);
    $diffInSeconds = abs($invitation->expires_at->diffInSeconds($expectedExpiry));

    expect($diffInSeconds)->toBeLessThan(2);
});

test('it belongs to the correct role and inviter', function () {
    $role = Role::factory()->create();
    $inviter = User::factory()->create();
    $action = new CreateInvitation();

    $invitation = $action->handle('test@example.com', $role->id, $inviter->id);

    expect($invitation->role->id)->toBe($role->id)
        ->and($invitation->inviter->id)->toBe($inviter->id);
});
