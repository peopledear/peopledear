<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Session;

test('user can mark notification as read', function (): void {
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

    expect($notification?->read_at)->toBeNull();

    $this->actingAs($user)
        ->post(route('notifications.mark-read', $notification))
        ->assertRedirect();

    expect($notification?->fresh()?->read_at)->not->toBeNull();
});

test('marking already read notification is idempotent', function (): void {
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
    $notification?->markAsRead();

    $originalReadAt = $notification?->fresh()?->read_at;

    $this->actingAs($user)
        ->post(route('notifications.mark-read', $notification))
        ->assertRedirect();

    expect($notification?->fresh()?->read_at?->toDateTimeString())
        ->toBe($originalReadAt?->toDateTimeString());
});

test('user cannot mark other users notification as read', function (): void {
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
        ->post(route('notifications.mark-read', $notification))
        ->assertForbidden();
});

test('unauthenticated user cannot mark notification as read', function (): void {
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

    $this->post(route('notifications.mark-read', $notification))
        ->assertRedirect(route('login'));
});
