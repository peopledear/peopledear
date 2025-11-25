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

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $employee->notify(new GeneralNotification('Test', 'Test message'));

    $notification = $employee->notifications()->first();

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

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $employee->notify(new GeneralNotification('Test', 'Test message'));

    $notification = $employee->notifications()->first();
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

    /** @var Employee $otherEmployee */
    $otherEmployee = Employee::factory()
        ->for($organization)
        ->for($otherUser)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $otherEmployee->notify(new GeneralNotification('Test', 'Test message'));

    $notification = $otherEmployee->notifications()->first();

    $this->actingAs($user)
        ->post(route('notifications.mark-read', $notification))
        ->assertForbidden();
});

test('unauthenticated user cannot mark notification as read', function (): void {
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $employee->notify(new GeneralNotification('Test', 'Test message'));

    $notification = $employee->notifications()->first();

    $this->post(route('notifications.mark-read', $notification))
        ->assertRedirect(route('login'));
});

test('user can delete their notification', function (): void {
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $employee->notify(new GeneralNotification('Test', 'Test message'));

    $notification = $employee->notifications()->first();

    expect($employee->notifications()->count())->toBe(1);

    $this->actingAs($user)
        ->delete(route('notifications.destroy', $notification))
        ->assertRedirect();

    expect($employee->notifications()->count())->toBe(0);
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

    /** @var Employee $otherEmployee */
    $otherEmployee = Employee::factory()
        ->for($organization)
        ->for($otherUser)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $otherEmployee->notify(new GeneralNotification('Test', 'Test message'));

    $notification = $otherEmployee->notifications()->first();

    $this->actingAs($user)
        ->delete(route('notifications.destroy', $notification))
        ->assertForbidden();
});
