<?php

declare(strict_types=1);

use App\Models\Invitation;
use App\Queries\PendingInvitationsQuery;
use Illuminate\Database\Eloquent\Builder;

test('it returns a query builder instance', function () {
    $query = new PendingInvitationsQuery();

    expect($query->builder())->toBeInstanceOf(Builder::class);
});

test('it eager loads role and inviter relationships', function () {
    $query = new PendingInvitationsQuery();
    $builder = $query->builder();

    expect($builder->getEagerLoads())->toHaveKey('role')
        ->and($builder->getEagerLoads())->toHaveKey('inviter');
});

test('it only returns pending invitations', function () {
    Invitation::factory()->pending()->count(3)->create();
    Invitation::factory()->accepted()->count(2)->create();
    Invitation::factory()->expired()->count(2)->create();

    $query = new PendingInvitationsQuery();
    $invitations = $query->builder()->get();

    expect($invitations)->toHaveCount(3);
    $invitations->each(function ($invitation) {
        expect($invitation->isPending())->toBeTrue();
    });
});

test('it excludes accepted invitations', function () {
    Invitation::factory()->accepted()->count(3)->create();

    $query = new PendingInvitationsQuery();
    $invitations = $query->builder()->get();

    expect($invitations)->toHaveCount(0);
});

test('it excludes expired invitations', function () {
    Invitation::factory()->expired()->count(3)->create();

    $query = new PendingInvitationsQuery();
    $invitations = $query->builder()->get();

    expect($invitations)->toHaveCount(0);
});

test('it orders invitations by created_at descending', function () {
    $invitation1 = Invitation::factory()->pending()->create(['created_at' => now()->subDays(2)]);
    $invitation2 = Invitation::factory()->pending()->create(['created_at' => now()->subDays(1)]);
    $invitation3 = Invitation::factory()->pending()->create(['created_at' => now()]);

    $query = new PendingInvitationsQuery();
    $invitations = $query->builder()->get();

    expect($invitations->first()->id)->toBe($invitation3->id)
        ->and($invitations->last()->id)->toBe($invitation1->id);
});
