<?php

declare(strict_types=1);

use App\Http\Middleware\ShareNotificationCount;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

it('shares notification count and recent notifications for authenticated user with employee', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'user_id' => $user->id,
        'organization_id' => $organization->id,
    ]);

    Notification::factory()->createQuietly([
        'notifiable_type' => Employee::class,
        'notifiable_id' => $employee->id,
        'organization_id' => $organization->id,
        'data' => [
            'title' => 'Test Title',
            'message' => 'Test Message',
            'action_url' => '/test-url',
        ],
        'read_at' => null,
    ]);

    Notification::factory()->createQuietly([
        'notifiable_type' => Employee::class,
        'notifiable_id' => $employee->id,
        'organization_id' => $organization->id,
        'data' => [
            'title' => 'Read Title',
            'message' => 'Read Message',
            'action_url' => null,
        ],
        'read_at' => now(),
    ]);

    $middleware = new ShareNotificationCount();

    $request = Request::create('/', 'GET');
    $request->setUserResolver(fn () => $user);

    $response = $middleware->handle($request, fn (): Response => new Response());

    expect($response)->toBeInstanceOf(Response::class);

    $props = Inertia::getShared();

    expect($props)->toHaveKey('notificationUnreadCount')
        ->and($props['notificationUnreadCount'])->toBe(1)
        ->and($props)->toHaveKey('recentNotifications')
        ->and($props['recentNotifications'])->toHaveCount(2);

    $notifications = $props['recentNotifications'];

    // Find the read notification
    $readNotification = collect($notifications)->first(fn ($n): bool => $n->read_at !== null);
    $unreadNotification = collect($notifications)->first(fn ($n): bool => $n->read_at === null);

    expect($readNotification)->not->toBeNull()
        ->and($readNotification->title)->toBe('Read Title')
        ->and($readNotification->message)->toBe('Read Message')
        ->and($readNotification->action_url)->toBeNull()
        ->and($unreadNotification)->not->toBeNull()
        ->and($unreadNotification->title)->toBe('Test Title')
        ->and($unreadNotification->message)->toBe('Test Message')
        ->and($unreadNotification->action_url)->toBe('/test-url');
});

it('does not share notifications for guest users', function (): void {
    $middleware = new ShareNotificationCount();

    $request = Request::create('/', 'GET');

    $response = $middleware->handle($request, fn (): Response => new Response());

    expect($response)->toBeInstanceOf(Response::class);

    $props = Inertia::getShared();

    expect($props)->not->toHaveKey('notificationUnreadCount')
        ->and($props)->not->toHaveKey('recentNotifications');
});

it('handles user without employee', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();

    $middleware = new ShareNotificationCount();

    $request = Request::create('/', 'GET');
    $request->setUserResolver(fn () => $user);

    $response = $middleware->handle($request, fn (): Response => new Response());

    expect($response)->toBeInstanceOf(Response::class);

    $props = Inertia::getShared();

    expect($props)->toHaveKey('notificationUnreadCount')
        ->and($props['notificationUnreadCount'])->toBe(0)
        ->and($props)->toHaveKey('recentNotifications')
        ->and($props['recentNotifications'])->toBeNull();
});

it('maps notification data with action_url', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'user_id' => $user->id,
        'organization_id' => $organization->id,
    ]);

    Notification::factory()->createQuietly([
        'notifiable_type' => Employee::class,
        'notifiable_id' => $employee->id,
        'organization_id' => $organization->id,
        'data' => [
            'title' => 'Action Title',
            'message' => 'Action Message',
            'action_url' => '/action-url',
        ],
    ]);

    $middleware = new ShareNotificationCount();

    $request = Request::create('/', 'GET');
    $request->setUserResolver(fn () => $user);

    $middleware->handle($request, fn (): Response => new Response());

    $props = Inertia::getShared();
    $notification = $props['recentNotifications'][0];

    expect($notification->action_url)->toBe('/action-url')
        ->and($notification->title)->toBe('Action Title')
        ->and($notification->message)->toBe('Action Message')
        ->and($notification->type)->toBe('GeneralNotification');
});
