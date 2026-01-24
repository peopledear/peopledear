<?php

declare(strict_types=1);

use App\Enums\LocationType;
use App\Models\Holiday;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

test('has a users relationship', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->create();

    expect($organization->users())
        ->toBeInstanceOf(HasMany::class);
});

test('has a periods relationship', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->create();

    expect($organization->periods())
        ->toBeInstanceOf(HasMany::class);
});

test('organization has correct fillable attributes', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->create([
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
    $organization = Organization::factory()->create();

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
    $organization = Organization::factory()->create([
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

test('organization has locations relationship', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->create();

    expect($organization->locations())
        ->toBeInstanceOf(HasMany::class);
});

test('organization offices relationship is properly loaded', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->create();

    Location::factory()
        ->for($organization)
        ->create([
            'name' => 'Headquarters',
            'type' => LocationType::Headquarters,
        ]);

    Location::factory()
        ->for($organization)
        ->create([
            'name' => 'Office 2',
            'type' => LocationType::Warehouse,
        ]);

    $organization->load('locations');

    expect($organization->locations)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->and($organization->locations->pluck('name')->toArray())
        ->toBe(['Headquarters', 'Office 2']);
});

test('organization has holidays relationship', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->create();

    expect($organization->holidays())->toBeInstanceOf(HasMany::class);
});

test('organization holidays relationship is properly loaded', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->create();

    Holiday::factory()->create([
        'organization_id' => $organization->id,
        'name' => ['en' => 'New Year'],
    ]);

    Holiday::factory()->create([
        'organization_id' => $organization->id,
        'name' => ['en' => 'Christmas'],
    ]);

    $organization->load('holidays');

    expect($organization->holidays)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2);
});

test('to array', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()
        ->create()
        ->refresh();

    expect(array_keys($organization->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'name',
            'identifier',
            'resource_key',
            'vat_number',
            'ssn',
            'phone',
        ]);
});

test('organization has headquarters relationship', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    expect($organization->headquarters())
        ->toBeInstanceOf(HasMany::class);
});

test('headquarters relationship only returns headquarters locations', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    Location::factory()->createQuietly([
        'organization_id' => $organization->id,
        'name' => 'US Headquarters',
        'type' => LocationType::Headquarters,
    ]);

    Location::factory()->createQuietly([
        'organization_id' => $organization->id,
        'name' => 'EU Headquarters',
        'type' => LocationType::Headquarters,
    ]);

    Location::factory()->createQuietly([
        'organization_id' => $organization->id,
        'name' => 'Branch Office',
        'type' => LocationType::Branch,
    ]);

    expect($organization->headquarters)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->and($organization->headquarters->pluck('name')->toArray())
        ->toContain('US Headquarters')
        ->toContain('EU Headquarters')
        ->not->toContain('Branch Office');
});

test('organization has headOffice relationship', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    expect($organization->headOffice())
        ->toBeInstanceOf(HasOne::class);
});

test('headOffice relationship returns latest headquarters', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var Location $oldHeadquarters */
    $oldHeadquarters = Location::factory()->createQuietly([
        'organization_id' => $organization->id,
        'name' => 'Old Headquarters',
        'type' => LocationType::Headquarters,
        'created_at' => now()->subYear(),
    ]);

    /** @var Location $newHeadquarters */
    $newHeadquarters = Location::factory()->createQuietly([
        'organization_id' => $organization->id,
        'name' => 'New Headquarters',
        'type' => LocationType::Headquarters,
        'created_at' => now(),
    ]);

    expect($organization->headOffice)
        ->toBeInstanceOf(Location::class)
        ->and($organization->headOffice->name)
        ->toBe('New Headquarters');
});

test('hasLocationConfigured returns true when headOffice exists', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    Location::factory()->createQuietly([
        'organization_id' => $organization->id,
        'type' => LocationType::Headquarters,
    ]);

    expect($organization->hasLocationConfigured())->toBeTrue();
});

test('hasLocationConfigured returns false when no headOffice exists', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    expect($organization->hasLocationConfigured())->toBeFalse();
});

test('hasLocationConfigured returns false when only branch locations exist', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    Location::factory()->createQuietly([
        'organization_id' => $organization->id,
        'type' => LocationType::Branch,
    ]);

    expect($organization->hasLocationConfigured())->toBeFalse();
});
