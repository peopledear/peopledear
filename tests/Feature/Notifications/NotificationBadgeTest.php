<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Session;

test('notification count is shared on authenticated pages', function (): void {
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

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('notificationUnreadCount')
            ->where('notificationUnreadCount', 2)
        );
});

test('notification count updates after marking as read', function (): void {
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

    $notification = $employee->notifications()->first();
    $this->actingAs($user)
        ->post(route('notifications.mark-read', $notification))
        ->assertRedirect();

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('notificationUnreadCount', 1)
        );
});

test('notification count is zero when no unread notifications', function (): void {
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('notificationUnreadCount', 0)
        );
});
