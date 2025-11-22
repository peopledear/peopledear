<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Session;
use Inertia\Testing\AssertableInertia as Assert;

test('user can view their notifications', function (): void {
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $user->notify(new GeneralNotification('Test Title', 'Test Message'));

    $this->actingAs($user)
        ->withSession(['current_organization' => $organization->id])
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('notifications/index')
            ->has('notifications.notifications', 1)
            ->where('notifications.notifications.0.title', 'Test Title')
            ->where('notifications.notifications.0.message', 'Test Message')
        );
});

test('user sees empty state when no notifications', function (): void {
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
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('notifications/index')
            ->has('notifications.notifications', 0)
        );
});

test('notifications are ordered newest first', function (): void {
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $user->notify(new GeneralNotification('First', 'First notification'));

    $this->travel(1)->minute();

    $user->notify(new GeneralNotification('Second', 'Second notification'));

    $this->actingAs($user)
        ->withSession(['current_organization' => $organization->id])
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('notifications/index')
            ->has('notifications.notifications', 2)
            ->where('notifications.notifications.0.title', 'Second')
            ->where('notifications.notifications.1.title', 'First')
        );
});

test('user cannot view other users notifications', function (): void {
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

    $otherUser->notify(new GeneralNotification('Other User Title', 'Other user message'));

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('notifications/index')
            ->has('notifications.notifications', 0)
        );
});

test('unauthenticated user cannot view notifications', function (): void {
    $this->get(route('notifications.index'))
        ->assertRedirect(route('login'));
});

test('notifications include unread count', function (): void {
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $user->notify(new GeneralNotification('Title 1', 'Message 1'));
    $user->notify(new GeneralNotification('Title 2', 'Message 2'));

    $this->actingAs($user)
        ->withSession(['current_organization' => $organization->id])
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('notifications/index')
            ->where('notifications.unreadCount', 2)
        );
});
