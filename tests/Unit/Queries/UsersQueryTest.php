<?php

declare(strict_types=1);

use App\Models\User;
use App\Queries\UsersQuery;
use Illuminate\Database\Eloquent\Builder;

test('it returns a query builder instance', function () {
    $query = new UsersQuery();

    expect($query->builder())->toBeInstanceOf(Builder::class);
});

test('it eager loads role relationship', function () {
    $query = new UsersQuery();
    $builder = $query->builder();

    expect($builder->getEagerLoads())->toHaveKey('role');
});

test('it orders users by created_at descending', function () {
    $user1 = User::factory()->create(['created_at' => now()->subDays(2)]);
    $user2 = User::factory()->create(['created_at' => now()->subDays(1)]);
    $user3 = User::factory()->create(['created_at' => now()]);

    $query = new UsersQuery();
    $users = $query->builder()->get();

    expect($users->first()->id)->toBe($user3->id)
        ->and($users->last()->id)->toBe($user1->id);
});

test('it returns all users', function () {
    User::factory()->count(5)->create();

    $query = new UsersQuery();
    $users = $query->builder()->get();

    expect($users)->toHaveCount(5);
});
