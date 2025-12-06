<?php

declare(strict_types=1);

use App\Enums\Support\SessionKey;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\ActionableNotification;
use Illuminate\Support\Facades\Session;

test('sends actionable notification', function (): void {
    $user = User::factory()
        ->create();

    $organization = Organization::factory()
        ->create();

    Session::put(SessionKey::CurrentOrganization->value, $organization->id);

    $notification = new ActionableNotification(
        title: 'Actionable Notification Title',
        message: 'This is an actionable notification message.',
        actionUrl: 'https://example.com/action',
    );

    $user->notify($notification);

    expect($user->notifications)->toHaveCount(1)
        ->and($user->notifications->first()?->data['title'])->toBe('Actionable Notification Title')
        ->and($user->notifications->first()?->data['action_url'])->toBe('https://example.com/action');
});
