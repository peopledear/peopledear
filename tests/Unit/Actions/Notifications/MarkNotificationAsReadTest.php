<?php

declare(strict_types=1);

use App\Actions\Notifications\MarkNotificationAsRead;
use App\Models\Notification;
use App\Models\User;

beforeEach(function (): void {
    $this->action = resolve(MarkNotificationAsRead::class);
});

test('marks unread notification as read',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()->createQuietly();

        /** @var Notification $notification */
        $notification = Notification::factory()->createQuietly([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'read_at' => null,
        ]);

        expect($notification->read_at)->toBeNull();

        $this->action->handle($notification);

        expect($notification->refresh()->read_at)->not->toBeNull();
    });

test('does not update already read notification',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()->createQuietly();

        $originalReadAt = now()->subHour();

        /** @var Notification $notification */
        $notification = Notification::factory()->createQuietly([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'read_at' => $originalReadAt,
        ]);

        $this->action->handle($notification);

        expect($notification->refresh()->read_at->timestamp)
            ->toBe($originalReadAt->timestamp);
    });
