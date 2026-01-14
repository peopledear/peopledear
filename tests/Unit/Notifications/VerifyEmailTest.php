<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\VerifyEmail;

it('generates tenant-aware verification URL', function (): void {
    /** @var User $user */
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $notification = new VerifyEmail;
    $reflectionMethod = new ReflectionMethod($notification, 'verificationUrl');
    $reflectionMethod->setAccessible(true);

    $url = $reflectionMethod->invoke($notification, $user);

    expect($url)
        ->toContain($user->organization->identifier.'.localhost') // subdomain format
        ->toContain('verify-email')
        ->toContain($user->getKey())
        ->toContain(sha1((string) $user->email))
        ->toContain('signature=');
});
