<?php

declare(strict_types=1);

use App\Models\Country;
use App\Models\Organization;

test('owner can create organization via modal', function (): void {
    Organization::query()->delete();

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
        ->fill('name', 'Test Organization')
        ->click('#country_id')
        ->click(sprintf("[data-slot='select-item']:has-text('%s')", $country->name['EN']))
        ->click('Create organization')
        ->wait(2) // Give time for form submission and redirect in CI
        ->assertNoJavascriptErrors();

    /** @var Organization|null $organization */
    $organization = Organization::query()->first();

    expect($organization)
        ->not->toBeNull()
        ->name->toBe('Test Organization')
        ->country_id->toBe($country->id);
});

test('owner can access org overview after organization is created', function (): void {

    $this->actingAs($this->owner);

    $page = visit('/org');

    $page->assertPathIs('/org')
        ->assertDontSee('New organization')
        ->assertNoJavascriptErrors();
});
