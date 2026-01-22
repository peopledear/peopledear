<?php

declare(strict_types=1);

use App\Models\Notification;
use App\Models\User;
use Sprout\Exceptions\MisconfigurationException;
use Symfony\Component\HttpFoundation\Response;

use function App\tenant_route;

test('index sends a list of notifications to inertia',
    /**
     * @throws MisconfigurationException
     */
    function (): void {

        /** @var User $user */
        $user = User::factory()
            ->create();

        Notification::factory()
            ->for($user, 'notifiable')
            ->count(10)
            ->create();

        $response = $this->actingAs($user)
            ->fromRoute('home')
            ->get(tenant_route('tenant.notifications.dropdown', $user->organization), [
                'X-Dropdown' => 'true',
            ]);

        expect($response->status())
            ->toBe(Response::HTTP_OK);

    });
