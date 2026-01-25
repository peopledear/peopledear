<?php

declare(strict_types=1);

use App\Models\User;
use App\Policies\UserPolicy;

beforeEach(function (): void {
    $this->policy = new UserPolicy;
});

test('allows viewing user in same organization',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()->createQuietly();

        /** @var User $otherUser */
        $otherUser = User::factory()->createQuietly([
            'organization_id' => $user->organization_id,
        ]);

        expect($this->policy->view($user, $otherUser))->toBeTrue();
    });

test('denies viewing user from different organization',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()->createQuietly();

        /** @var User $otherUser */
        $otherUser = User::factory()->createQuietly();

        expect($this->policy->view($user, $otherUser))->toBeFalse();
    });
