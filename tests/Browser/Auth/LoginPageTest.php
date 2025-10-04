<?php

declare(strict_types=1);

it('renders the login page', function (): void {
    $page = visit('/login');

    expect($page->assertSee('Welcome'));
});
