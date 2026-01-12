<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia;

test('renders registration page', function (): void {
    $response = $this->get(route('auth.register'));

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page->component('register/create')
    );
});
