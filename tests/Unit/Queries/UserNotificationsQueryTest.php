<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Queries\UserNotificationsQuery;
use Illuminate\Support\Facades\Session;

test('query returns notifications for user', function (): void {
    $organization = Organization::factory()->create();

    /** @var User $user */
    $user = User::factory()->create();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->create();

    Session::put('current_organization', $organization->id);

    $user->notify(new GeneralNotification('Test', 'Test message'));

    $this->actingAs($user);

    /** @var UserNotificationsQuery $query */
    $query = app(UserNotificationsQuery::class);
    $notifications = $query->builder()->get();

    expect($notifications)->toHaveCount(1)
        ->and($notifications->first()->data['title'])->toBe('Test');
});

test('query returns unread count', function (): void {
    $organization = Organization::factory()->create();

    /** @var User $user */
    $user = User::factory()->create();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->create();

    Session::put('current_organization', $organization->id);

    $user->notify(new GeneralNotification('Test 1', 'Message 1'));
    $user->notify(new GeneralNotification('Test 2', 'Message 2'));

    $this->actingAs($user);

    /** @var UserNotificationsQuery $query */
    $query = app(UserNotificationsQuery::class);

    expect($query->unreadCount())->toBe(2);
});

test('query orders unread notifications first', function (): void {
    $organization = Organization::factory()->create();

    /** @var User $user */
    $user = User::factory()->create();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->create();

    Session::put('current_organization', $organization->id);

    $user->notify(new GeneralNotification('First', 'First message'));

    $this->travel(1)->minute();

    $user->notify(new GeneralNotification('Second', 'Second message'));

    // Mark first notification as read
    $user->notifications()->reorder()->oldest()->first()?->markAsRead();

    $this->actingAs($user);

    /** @var UserNotificationsQuery $query */
    $query = app(UserNotificationsQuery::class);
    $notifications = $query->builder()->get();

    // Unread (Second) should come before read (First)
    expect($notifications->first()->data['title'])->toBe('Second')
        ->and($notifications->last()->data['title'])->toBe('First');
});

test('unread count returns zero for no notifications', function (): void {
    $organization = Organization::factory()->create();

    /** @var User $user */
    $user = User::factory()->create();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->create();

    $this->actingAs($user);

    /** @var UserNotificationsQuery $query */
    $query = app(UserNotificationsQuery::class);

    expect($query->unreadCount())->toBe(0);
});

test('unread count excludes read notifications', function (): void {
    $organization = Organization::factory()->create();

    /** @var User $user */
    $user = User::factory()->create();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->create();

    Session::put('current_organization', $organization->id);

    $user->notify(new GeneralNotification('Test 1', 'Message 1'));
    $user->notify(new GeneralNotification('Test 2', 'Message 2'));

    // Mark one as read
    $user->notifications()->first()?->markAsRead();

    $this->actingAs($user);

    /** @var UserNotificationsQuery $query */
    $query = app(UserNotificationsQuery::class);

    expect($query->unreadCount())->toBe(1);
});
