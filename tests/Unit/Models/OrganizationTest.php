<?php

declare(strict_types=1);

use App\Models\Country;
use App\Models\Holiday;
use App\Models\Office;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

test('has a periods relationship', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    expect($organization->periods())
        ->toBeInstanceOf(HasMany::class);
});

test('organization has correct fillable attributes', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'name' => 'Test Company',
        'vat_number' => '1234567890',
        'ssn' => '12-3456789',
        'phone' => '+1234567890',
    ]);

    expect($organization->name)
        ->toBe('Test Company')
        ->and($organization->vat_number)
        ->toBe('1234567890')
        ->and($organization->ssn)
        ->toBe('12-3456789')
        ->and($organization->phone)
        ->toBe('+1234567890');
});

test('organization model has correct casts', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    $casts = $organization->getCasts();

    expect($casts)
        ->toBeArray()
        ->toHaveKey('name')
        ->toHaveKey('vat_number')
        ->toHaveKey('ssn')
        ->toHaveKey('phone');
});

test('organization can be created with only required fields', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'name' => 'Minimal Org',
        'vat_number' => null,
        'ssn' => null,
        'phone' => null,
    ]);

    expect($organization->name)
        ->toBe('Minimal Org')
        ->and($organization->vat_number)
        ->toBeNull()
        ->and($organization->ssn)
        ->toBeNull()
        ->and($organization->phone)
        ->toBeNull();
});

test('organization has offices relationship', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    expect($organization->offices())->toBeInstanceOf(HasMany::class);
});

test('organization offices relationship is properly loaded', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var Office $office1 */
    $office1 = Office::factory()->createQuietly([
        'organization_id' => $organization->id,
        'name' => 'Office 1',
    ]);

    /** @var Office $office2 */
    $office2 = Office::factory()->createQuietly([
        'organization_id' => $organization->id,
        'name' => 'Office 2',
    ]);

    $organization->load('offices');

    expect($organization->offices)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->and($organization->offices->pluck('name')->toArray())
        ->toBe(['Office 1', 'Office 2']);
});

test('organization has holidays relationship', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    expect($organization->holidays())->toBeInstanceOf(HasMany::class);
});

test('organization holidays relationship is properly loaded', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var Holiday $holiday1 */
    $holiday1 = Holiday::factory()->createQuietly([
        'organization_id' => $organization->id,
        'name' => ['en' => 'New Year'],
    ]);

    /** @var Holiday $holiday2 */
    $holiday2 = Holiday::factory()->createQuietly([
        'organization_id' => $organization->id,
        'name' => ['en' => 'Christmas'],
    ]);

    $organization->load('holidays');

    expect($organization->holidays)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2);
});

test('organization has country relationship', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    expect($organization->country())->toBeInstanceOf(BelongsTo::class);
});

test('organization country relationship is properly loaded', function (): void {
    /** @var Country $country */
    $country = Country::factory()->createQuietly([
        'iso_code' => 'US',
        'name' => ['en' => 'United States'],
        'official_languages' => ['en'],
    ]);

    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'country_id' => $country->id,
    ]);

    $organization->load('country');

    expect($organization->country)
        ->toBeInstanceOf(Country::class)
        ->and($organization->country->iso_code)
        ->toBe('US');
});

test('organization can exist without country', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'country_id' => null,
    ]);

    expect($organization->country_id)
        ->toBeNull()
        ->and($organization->country)
        ->toBeNull();
});

test('has location configured returns true when country is set', function (): void {
    /** @var Country $country */
    $country = Country::factory()->createQuietly();

    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'country_id' => $country->id,
    ]);

    expect($organization->hasLocationConfigured())->toBeTrue();
});

test('has location configured returns false when country is null', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'country_id' => null,
    ]);

    expect($organization->hasLocationConfigured())->toBeFalse();
});

test('to array', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()
        ->createQuietly()
        ->refresh();

    expect(array_keys($organization->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'name',
            'vat_number',
            'ssn',
            'phone',
            'country_id',
        ]);
});
