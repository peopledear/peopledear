<?php

declare(strict_types=1);

use App\Models\Notification;
use App\Notifications\GeneralNotification;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

test('user can delete notification',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $this->employee->notify(new GeneralNotification('Test', 'Test message'));

        $notification = Notification::query()
            ->find($this->employee->notifications()->first()->id);

        expect($this->employee->notifications()->count())->toBe(1);

        $this->actingAs($this->employee)
            ->fromRoute('home')
            ->delete(tenant_route('tenant.notifications.destroy', $this->tenant, [
                'notification' => $notification,
            ]));

        expect($this->employee->notifications()->count())->toBe(0);
    });

test('deleted notification does not reappear',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $this->employee->notify(new GeneralNotification('Test', 'Test message'));

        $notification = Notification::query()
            ->find($this->employee->notifications()->first()->id);
        $notificationId = $notification->id;

        $this->actingAs($this->employee)
            ->fromRoute('home')
            ->delete(tenant_route('tenant.notifications.destroy', $this->tenant, [
                'notification' => $notification,
            ]));

        expect($this->employee->notifications()->where('id', $notificationId)->exists())->toBeFalse();
    });

test('user cannot delete other users notification',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $this->peopleManager->notify(new GeneralNotification('Test', 'Test message'));

        $notification = Notification::query()
            ->find($this->peopleManager->notifications()->first()->id);

        $this->actingAs($this->employee)
            ->fromRoute('home')
            ->delete(tenant_route('tenant.notifications.destroy', $this->tenant, [
                'notification' => $notification,
            ]))
            ->assertForbidden();

        expect($this->peopleManager->notifications()->count())->toBe(1);
    });

test('unauthenticated user cannot delete notification',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $this->employee->notify(new GeneralNotification('Test', 'Test message'));

        $notification = Notification::query()
            ->find($this->employee->notifications()->first()->id);

        $this->fromRoute('home')
            ->delete(tenant_route('tenant.notifications.destroy', $this->tenant, [
                'notification' => $notification,
            ]))
            ->assertRedirect(tenant_route('tenant.auth.login', $this->tenant));
    });
