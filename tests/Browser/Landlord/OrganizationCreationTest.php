<?php

declare(strict_types=1);

use App\Models\Country;

test('owner can create organization', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'name' => [
            'EN' => 'Testland',
            'FR' => 'Testpays',
        ],
    ]);

    $this->actingAs($this->owner);

    $page = visit('/org/create');

    $page->assertSee('New organization')
        ->assertSee('Organization name')
        ->assertSee('Country')
        ->assertSee('Create organization')
        ->fill('name', 'Test Organization');

    // Open the select and verify it shows the country option
    $page->click('[role="combobox"]');
    $page->assertSee('Testland');

    // Note: Country selection is UI-only currently, not saved to database
    // See CreateOrganizationData - only 'name' is persisted
    $page->click('[role="option"]:has-text("Testland")');
    $page->click('Create organization')
        ->assertNoJavascriptErrors();

    /** @var App\Models\Organization|null $organization */
    $organization = App\Models\Organization::query()->where('name', 'Test Organization')->first();

    expect($organization)
        ->not->toBeNull()
        ->name->toBe('Test Organization');
});

test('owner can access org overview after organization is created', function (): void {

    $this->actingAs($this->owner);

    $page = visit('/org');

    $page->assertPathIs('/org')
        ->assertDontSee('New organization')
        ->assertNoJavascriptErrors();
});
