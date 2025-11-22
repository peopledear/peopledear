<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Session;

test('user can delete notification', function (): void {
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $user->notify(new GeneralNotification('Test', 'Test message'));

    $notification = $user->notifications()->first();

    expect($user->notifications()->count())->toBe(1);

    $this->actingAs($user)
        ->delete(route('notifications.destroy', $notification))
        ->assertRedirect();

    expect($user->notifications()->count())->toBe(0);
});

test('deleted notification does not reappear', function (): void {
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $user->notify(new GeneralNotification('Test', 'Test message'));

    $notification = $user->notifications()->first();
    $notificationId = $notification?->id;

    $this->actingAs($user)
        ->delete(route('notifications.destroy', $notification))
        ->assertRedirect();

    expect($user->notifications()->where('id', $notificationId)->exists())->toBeFalse();
});

test('user cannot delete other users notification', function (): void {
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    /** @var User $otherUser */
    $otherUser = User::factory()->createQuietly();

    Employee::factory()
        ->for($organization)
        ->for($otherUser)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $otherUser->notify(new GeneralNotification('Test', 'Test message'));

    $notification = $otherUser->notifications()->first();

    $this->actingAs($user)
        ->delete(route('notifications.destroy', $notification))
        ->assertForbidden();

    expect($otherUser->notifications()->count())->toBe(1);
});

test('unauthenticated user cannot delete notification', function (): void {
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $user->notify(new GeneralNotification('Test', 'Test message'));

    $notification = $user->notifications()->first();

    $this->delete(route('notifications.destroy', $notification))
        ->assertRedirect(route('login'));
});
