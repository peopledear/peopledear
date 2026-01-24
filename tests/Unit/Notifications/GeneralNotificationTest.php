<?php

declare(strict_types=1);

use App\Enums\SessionKey;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Session;

test('sends general notification', function (): void {
    $user = User::factory()
        ->create();

    $organization = Organization::factory()
        ->create();

    Session::put(SessionKey::CurrentOrganization->value, $organization->id);

    $notification = new GeneralNotification(
        title: 'General Notification Title',
        message: 'This is a general notification message.',
    );

    $user->notify($notification);

    expect($user->notifications)->toHaveCount(1)
        ->and($user->notifications->first()?->data['title'])->toBe('General Notification Title');
});
