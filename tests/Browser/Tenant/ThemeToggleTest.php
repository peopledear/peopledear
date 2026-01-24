<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;

test('renders theme toggle buttons in user menu',
    /**
     * @throws Throwable
     */
    function (): void {
        Auth::login($this->employee);

        $page = visit(route(
            name: 'tenant.user.settings.profile.edit',
            parameters: ['tenant' => $this->tenant->identifier],
            absolute: false
        ));

        // Verify we're on the profile settings page
        $page->assertSee('Profile');

        // Open user menu by clicking the avatar/user button
        $page->click('button:has([data-slot="avatar"])');

        // Verify theme toggle group is visible
        $page->assertSee('Theme');

        // Verify all three theme buttons are present using aria-labels
        $page->assertVisible('[aria-label="Light theme"]');
        $page->assertVisible('[aria-label="Dark theme"]');
        $page->assertVisible('[aria-label="System theme"]');

        // No JavaScript errors
        $page->assertNoJavascriptErrors();
    });

test('can select dark theme from user menu',
    /**
     * @throws Throwable
     */
    function (): void {
        Auth::login($this->employee);

        $page = visit(route(
            name: 'tenant.user.settings.profile.edit',
            parameters: ['tenant' => $this->tenant->identifier],
            absolute: false
        ));

        // Open user menu by clicking the avatar/user button
        $page->click('button:has([data-slot="avatar"])');

        // Click on Dark theme toggle button
        $page->click('[aria-label="Dark theme"]');

        // Wait a moment for the theme to apply
        $page->wait(1);

        // Verify dark class is applied using script
        $hasDarkClass = $page->script('document.documentElement.classList.contains("dark")');
        expect($hasDarkClass)->toBeTrue();

        // No JavaScript errors
        $page->assertNoJavascriptErrors();
    });
