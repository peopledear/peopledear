<?php

declare(strict_types=1);

use App\Models\User;

it('returns validation exception if wrong credentials are given', function (): void {

    $response = $this->post(route('auth.login.store'), [
        'email' => 'nonexistinguser@example.com',
        'password' => 'password',
        'remember' => false,
    ]);

    expect($response->getStatusCode())
        ->toBe(302)
        ->and($response->assertRedirectBackWithErrors());

});

it('does not allow the user to login', function (): void {

    // Arrange
    /** @var User $user */
    $user = User::factory()->createQuietly([
        'password' => 'password',
    ]);

    auth()->login($user);

    $response = $this->post(route('auth.login.store'), [
        'email' => $user->email,
        'password' => 'password',
        'remember' => false,
    ]);

    expect($response->getStatusCode())
        ->toBe(302)
        ->and($response->assertRedirectToRoute('dashboard'));

});

it('authenticates the user', function (): void {

    // Arrange
    /** @var User $user */
    $user = User::factory()->createQuietly([
        'password' => 'password',
    ]);

    // Act
    $response = $this->post(route('auth.login.store'), [
        'email' => $user->email,
        'password' => 'password',
        'remember' => false,
    ]);

    // Assert
    expect($response->getStatusCode())
        ->toBe(302)
        ->and($this->assertAuthenticatedAs($user));

});
