<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

test('user can mark own notification as read',
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
            ->createQuietly();

        $user->notify(new GeneralNotification('Test Title', 'Test message'));

        /** @var Notification $notification */
        $notification = $user->notifications()->first();

        expect($notification->read_at)->toBeNull();

        $this->actingAs($user)
            ->post(tenant_route('tenant.notifications.mark-read', $this->tenant, [
                'notification' => $notification->id,
            ]))
            ->assertRedirect();

        expect($notification->refresh()->read_at)->not->toBeNull();
    });

test('user cannot mark another users notification as read',
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
            ->createQuietly();

        /** @var User $otherUser */
        $otherUser = User::factory()
            ->for($this->tenant, 'organization')
            ->create();

        Employee::factory()
            ->for($this->tenant)
            ->for($otherUser)
            ->createQuietly();

        $otherUser->notify(new GeneralNotification('Other Title', 'Other message'));

        /** @var Notification $notification */
        $notification = $otherUser->notifications()->first();

        $this->actingAs($user)
            ->post(tenant_route('tenant.notifications.mark-read', $this->tenant, [
                'notification' => $notification->id,
            ]))
            ->assertForbidden();

        expect($notification->refresh()->read_at)->toBeNull();
    });

test('unauthenticated user cannot mark notification as read',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        $user->notify(new GeneralNotification('Test Title', 'Test message'));

        /** @var Notification $notification */
        $notification = $user->notifications()->first();

        $this->post(tenant_route('tenant.notifications.mark-read', $this->tenant, [
            'notification' => $notification->id,
        ]))
            ->assertRedirect(tenant_route('tenant.auth.login', $this->tenant));
    });
