<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Auth;

it('logs out the user', function (): void {

    /** @var User $user */
    $user = User::factory()
        ->createQuietly();

    Auth::login($user);

    $response = $this->post(route('auth.logout.store'));

    expect($response->isRedirect('/login'))
        ->and(Auth::check())
        ->toBeFalse();

});
