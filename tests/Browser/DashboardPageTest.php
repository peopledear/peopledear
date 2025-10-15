<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Auth;

it('renders the dashboard page', function (): void {

    $user = User::factory()
        ->createQuietly();

    Auth::login($user);

    $this->actingAs($user);

    $page = visit('/dashboard');

    expect($page->assertSee('Dashboard'));
});
