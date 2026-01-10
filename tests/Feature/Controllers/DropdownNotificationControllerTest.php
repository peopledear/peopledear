<?php

declare(strict_types=1);

use App\Models\Notification;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

test('index sends a list of notifications to inertia', function (): void {

    /** @var User $user */
    $user = User::factory()
        ->create();

    Notification::factory()
        ->for($user, 'notifiable')
        ->count(10)
        ->create();

    $response = $this->actingAs($user)
        ->get(route('notifications.dropdown'), [
            'X-Dropdown' => 'true',
        ]);

    expect($response->status())
        ->toBe(Response::HTTP_OK);

});
