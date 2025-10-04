<?php

declare(strict_types=1);

use App\Models\User;

it('renders the dashboard page', function (): void {

    $user = User::factory()
        ->createQuietly();
    auth()->login($user);

    $this->actingAs($user);

    $page = visit('/dashboard');

    expect($page->assertSee('dashboard'));
});
