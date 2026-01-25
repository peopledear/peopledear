<?php

declare(strict_types=1);

use App\Actions\User\UpdateUser;
use App\Models\User;

test('updates a user',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()->createQuietly([
            'name' => 'Old Name',
            'email' => 'old@email.com',
        ]);

        /** @var UpdateUser $action */
        $action = resolve(UpdateUser::class);

        $action->handle($user, [
            'name' => 'New Name',
        ]);

        expect($user->refresh()->name)->toBe('New Name')
            ->and($user->email)->toBe('old@email.com');
    });

test('resets email verification when email changes',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()->createQuietly([
            'email' => 'old@email.com',
            'email_verified_at' => now(),
        ]);

        expect($user->email_verified_at)->not->toBeNull();

        /** @var UpdateUser $action */
        $action = resolve(UpdateUser::class);

        $action->handle($user, [
            'email' => 'new@email.com',
        ]);

        expect($user->refresh()->email)->toBe('new@email.com')
            ->and($user->email_verified_at)->toBeNull();
    });

test('keeps email verification when email stays the same',
    /**
     * @throws Throwable
     */
    function (): void {
        $verifiedAt = now();

        /** @var User $user */
        $user = User::factory()->createQuietly([
            'email' => 'same@email.com',
            'email_verified_at' => $verifiedAt,
        ]);

        /** @var UpdateUser $action */
        $action = resolve(UpdateUser::class);

        $action->handle($user, [
            'email' => 'same@email.com',
            'name' => 'Updated Name',
        ]);

        expect($user->refresh()->email_verified_at)->not->toBeNull()
            ->and($user->name)->toBe('Updated Name');
    });
