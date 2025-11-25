<?php

declare(strict_types=1);

use App\Data\PeopleDear\Notification\NotificationData;
use App\Models\Notification;
use Illuminate\Support\Collection;

test('has a human readable time ago', function (): void {

    $notification = Notification::factory()->make([
        'data' => [
            'title' => 'Sample Notification',
            'message' => 'This is a test notification message.',
            'action_url' => 'https://example.com/action',
        ],
        'created_at' => now()->subDays(5),
    ]);

    $data = NotificationData::fromModel($notification);

    expect($data->created_ago)
        ->toBe('5 days ago');

});

test('creates a collection from eloquent collection', function (): void {

    $notifications = Notification::factory()
        ->count(15)
        ->create();

    $dataCollection = NotificationData::fromEloquentCollection($notifications);

    expect($dataCollection)
        ->toBeInstanceOf(Collection::class);

});
