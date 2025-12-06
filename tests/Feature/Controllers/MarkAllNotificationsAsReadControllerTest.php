<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Session;

test('user can mark all notifications as read', function (): void {
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
    $user->notify(new GeneralNotification('Test 3', 'Message 3'));

    expect($user->unreadNotifications()->count())->toBe(3);

    $this->actingAs($user)
        ->post(route('notifications.mark-all-read'))
        ->assertRedirect();

    expect($user->unreadNotifications()->count())->toBe(0);
});

test('mark all as read with no notifications does not error', function (): void {
    $organization = Organization::factory()->create();

    /** @var User $user */
    $user = User::factory()->create();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->create();

    $this->actingAs($user)
        ->post(route('notifications.mark-all-read'))
        ->assertRedirect();

    expect($user->unreadNotifications()->count())->toBe(0);
});

test('mark all as read only affects current users notifications', function (): void {
    $organization = Organization::factory()->create();

    /** @var User $user */
    $user = User::factory()->create();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->create();

    /** @var User $otherUser */
    $otherUser = User::factory()->create();

    Employee::factory()
        ->for($organization)
        ->for($otherUser)
        ->create();

    Session::put('current_organization', $organization->id);

    $user->notify(new GeneralNotification('User Test', 'User message'));
    $otherUser->notify(new GeneralNotification('Other Test', 'Other message'));

    $this->actingAs($user)
        ->post(route('notifications.mark-all-read'))
        ->assertRedirect();

    expect($user->unreadNotifications()->count())->toBe(0)
        ->and($otherUser->unreadNotifications()->count())->toBe(1);
});

test('unauthenticated user cannot mark all notifications as read', function (): void {
    $this->post(route('notifications.mark-all-read'))
        ->assertRedirect(route('login'));
});
