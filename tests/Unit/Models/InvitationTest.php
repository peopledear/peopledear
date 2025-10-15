<?php

declare(strict_types=1);

use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('invitation can be created', function () {
    $role = Role::factory()->create();
    $inviter = User::factory()->create();

    $invitation = Invitation::factory()
        ->for($role)
        ->for($inviter, 'inviter')
        ->create();

    expect($invitation)->toBeInstanceOf(Invitation::class)
        ->and($invitation->id)->toBeInt()
        ->and($invitation->email)->toBeString()
        ->and($invitation->token)->toBeString()
        ->and($invitation->role_id)->toBe($role->id)
        ->and($invitation->invited_by)->toBe($inviter->id)
        ->and($invitation->expires_at)->toBeInstanceOf(CarbonInterface::class)
        ->and($invitation->created_at)->toBeInstanceOf(CarbonInterface::class)
        ->and($invitation->updated_at)->toBeInstanceOf(CarbonInterface::class);
});

test('invitation has role relationship', function () {
    $invitation = Invitation::factory()->create();

    expect($invitation->role())->toBeInstanceOf(BelongsTo::class);
});

test('invitation has inviter relationship', function () {
    $invitation = Invitation::factory()->create();

    expect($invitation->inviter())->toBeInstanceOf(BelongsTo::class);
});

test('invitation can belong to a role', function () {
    $role = Role::factory()->create();
    $invitation = Invitation::factory()
        ->for($role)
        ->create();

    expect($invitation->role_id)->toBe($role->id)
        ->and($invitation->role->id)->toBe($role->id)
        ->and($invitation->role->name)->toBe($role->name);
});

test('invitation can belong to an inviter', function () {
    $inviter = User::factory()->create();
    $invitation = Invitation::factory()
        ->for($inviter, 'inviter')
        ->create();

    expect($invitation->invited_by)->toBe($inviter->id)
        ->and($invitation->inviter->id)->toBe($inviter->id)
        ->and($invitation->inviter->name)->toBe($inviter->name);
});

test('isPending returns true for pending invitations', function () {
    $invitation = Invitation::factory()
        ->pending()
        ->create();

    expect($invitation->isPending())->toBeTrue();
});

test('isPending returns false for expired invitations', function () {
    $invitation = Invitation::factory()
        ->expired()
        ->create();

    expect($invitation->isPending())->toBeFalse();
});

test('isPending returns false for accepted invitations', function () {
    $invitation = Invitation::factory()
        ->accepted()
        ->create();

    expect($invitation->isPending())->toBeFalse();
});

test('isExpired returns true for expired invitations', function () {
    $invitation = Invitation::factory()
        ->expired()
        ->create();

    expect($invitation->isExpired())->toBeTrue();
});

test('isExpired returns false for non-expired invitations', function () {
    $invitation = Invitation::factory()
        ->pending()
        ->create();

    expect($invitation->isExpired())->toBeFalse();
});

test('isAccepted returns true for accepted invitations', function () {
    $invitation = Invitation::factory()
        ->accepted()
        ->create();

    expect($invitation->isAccepted())->toBeTrue();
});

test('isAccepted returns false for pending invitations', function () {
    $invitation = Invitation::factory()
        ->pending()
        ->create();

    expect($invitation->isAccepted())->toBeFalse();
});

test('accept sets accepted_at timestamp', function () {
    $invitation = Invitation::factory()
        ->pending()
        ->create();

    expect($invitation->accepted_at)->toBeNull();

    $invitation->accept();

    expect($invitation->fresh()->accepted_at)->toBeInstanceOf(CarbonInterface::class);
});

test('pending factory state creates pending invitation', function () {
    $invitation = Invitation::factory()
        ->pending()
        ->create();

    expect($invitation->accepted_at)->toBeNull()
        ->and($invitation->isPending())->toBeTrue()
        ->and($invitation->isExpired())->toBeFalse();
});

test('accepted factory state creates accepted invitation', function () {
    $invitation = Invitation::factory()
        ->accepted()
        ->create();

    expect($invitation->accepted_at)->toBeInstanceOf(CarbonInterface::class)
        ->and($invitation->isAccepted())->toBeTrue()
        ->and($invitation->isPending())->toBeFalse();
});

test('expired factory state creates expired invitation', function () {
    $invitation = Invitation::factory()
        ->expired()
        ->create();

    expect($invitation->isExpired())->toBeTrue()
        ->and($invitation->isPending())->toBeFalse();
});

test('to array', function () {
    $invitation = Invitation::factory()
        ->create()
        ->refresh();

    expect(array_keys($invitation->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'email',
            'role_id',
            'invited_by',
            'token',
            'expires_at',
            'accepted_at',
        ]);
});
