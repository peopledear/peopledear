<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

test('user can mark all notifications as read',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create();

        Employee::factory()
            ->for($this->tenant)
            ->for($user)
            ->create();

        $user->notify(new GeneralNotification('Test 1', 'Message 1'));
        $user->notify(new GeneralNotification('Test 2', 'Message 2'));
        $user->notify(new GeneralNotification('Test 3', 'Message 3'));

        expect($user->unreadNotifications()->count())->toBe(3);

        $this->actingAs($user)
            ->post(tenant_route('tenant.notifications.mark-all-read', $this->tenant))
            ->assertRedirect();

        expect($user->refresh()->unreadNotifications()->count())->toBe(0);
    });

test('mark all as read with no notifications does not error',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create();

        Employee::factory()
            ->for($this->tenant)
            ->for($user)
            ->create();

        $this->actingAs($user)
            ->post(tenant_route('tenant.notifications.mark-all-read', $this->tenant))
            ->assertRedirect();

        expect($user->unreadNotifications()->count())->toBe(0);
    });

test('mark all as read only affects current users notifications',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create();

        Employee::factory()
            ->for($this->tenant)
            ->for($user)
            ->create();

        /** @var User $otherUser */
        $otherUser = User::factory()
            ->for($this->tenant, 'organization')
            ->create();

        Employee::factory()
            ->for($this->tenant)
            ->for($otherUser)
            ->create();

        $user->notify(new GeneralNotification('User Test', 'User message'));
        $otherUser->notify(new GeneralNotification('Other Test', 'Other message'));

        $this->actingAs($user)
            ->post(tenant_route('tenant.notifications.mark-all-read', $this->tenant))
            ->assertRedirect();

        expect($user->refresh()->unreadNotifications()->count())->toBe(0)
            ->and($otherUser->refresh()->unreadNotifications()->count())->toBe(1);
    });

test('unauthenticated user cannot mark all notifications as read',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $this->post(tenant_route('tenant.notifications.mark-all-read', $this->tenant))
            ->assertRedirect(tenant_route('tenant.auth.login', $this->tenant));
    });
