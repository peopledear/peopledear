<?php

declare(strict_types=1);

test('opens tenant welcome page with a subdomain', function (): void {

    visit('/')
        ->assertSee($this->tenant->name)
        ->assertDontSee('Globex');

});
