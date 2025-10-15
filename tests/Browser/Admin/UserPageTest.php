<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Auth;

beforeEach(function (): void {

    $this->user = User::factory()
        ->createQuietly();

    Auth::login($this->user);

});

test('may show the users page', function (): void {

    $page = visit(route('users.index'));

    expect($page->assertSee('Users'));

});
