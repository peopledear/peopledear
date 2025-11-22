<?php

declare(strict_types=1);

use App\Enums\Support\SessionKey;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\AlertNotification;
use Illuminate\Support\Facades\Session;

test('sends alert notification', function (): void {
    $user = User::factory()
        ->createQuietly();

    $organization = Organization::factory()
        ->createQuietly();

    Session::put(SessionKey::CurrentOrganization->value, $organization->id);

    $notification = new AlertNotification(
        title: 'Alert Notification Title',
        message: 'This is an alert notification message.',
    );

    $user->notify($notification);

    expect($user->notifications)->toHaveCount(1)
        ->and($user->notifications->first()?->data['title'])->toBe('Alert Notification Title')
        ->and($user->notifications->first()?->data['action_url'])->toBeNull();
});

test('sends alert notification with action url', function (): void {
    $user = User::factory()
        ->createQuietly();

    $organization = Organization::factory()
        ->createQuietly();

    Session::put(SessionKey::CurrentOrganization->value, $organization->id);

    $notification = new AlertNotification(
        title: 'Alert Title',
        message: 'Alert message.',
        actionUrl: 'https://example.com/alert',
    );

    $user->notify($notification);

    expect($user->notifications)->toHaveCount(1)
        ->and($user->notifications->first()?->data['action_url'])->toBe('https://example.com/alert');
});
