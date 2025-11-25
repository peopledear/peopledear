<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Session;

test('user can mark all notifications as read', function (): void {
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $employee->notify(new GeneralNotification('Test 1', 'Message 1'));
    $employee->notify(new GeneralNotification('Test 2', 'Message 2'));
    $employee->notify(new GeneralNotification('Test 3', 'Message 3'));

    expect($employee->unreadNotifications()->count())->toBe(3);

    $this->actingAs($user)
        ->post(route('notifications.mark-all-read'))
        ->assertRedirect();

    expect($employee->unreadNotifications()->count())->toBe(0);
});

test('mark all as read with no notifications does not error', function (): void {
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    $this->actingAs($user)
        ->post(route('notifications.mark-all-read'))
        ->assertRedirect();

    expect($user->unreadNotifications()->count())->toBe(0);
});

test('mark all as read only affects current users notifications', function (): void {
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()
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

    $employee->notify(new GeneralNotification('User Test', 'User message'));
    $otherEmployee->notify(new GeneralNotification('Other Test', 'Other message'));

    $this->actingAs($user)
        ->post(route('notifications.mark-all-read'))
        ->assertRedirect();

    expect($employee->unreadNotifications()->count())->toBe(0)
        ->and($otherEmployee->unreadNotifications()->count())->toBe(1);
});

test('unauthenticated user cannot mark all notifications as read', function (): void {
    $this->post(route('notifications.mark-all-read'))
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

    $employee->notify(new GeneralNotification('Test', 'Message'));

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

    /** @var Employee $employee */
    $employee = Employee::factory()
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

    $otherEmployee->notify(new GeneralNotification('Test', 'Message'));

    $notification = $otherEmployee->notifications()->first();

    $this->actingAs($user)
        ->delete(route('notifications.destroy', $notification))
        ->assertForbidden();
});
