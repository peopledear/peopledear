<?php

declare(strict_types=1);

use App\Enums\LocationType;
use App\Models\Country;
use App\Models\Location;
use App\Models\Organization;
use App\Queries\LocationQuery;
use Illuminate\Contracts\Container\BindingResolutionException;

beforeEach(
    /**
     * @throws BindingResolutionException
     */
    function (): void {

        $this->query = app()->make(LocationQuery::class);

    });

test('returns a collections of locations', function (): void {

    /** @var Organization $organization */
    $organization = Organization::factory()
        ->create();

    Location::factory()
        ->for($organization)
        ->count(3)
        ->create();

    $locations = ($this->query)()
        ->ofOrganization($organization)
        ->get();

    expect($locations)
        ->toHaveCount(3)
        ->and($locations->first()?->organization_id)
        ->toBe($organization->id);
});

test('check if a organization exists', function (): void {

    /** @var Organization $organization */
    $organization = Organization::factory()
        ->create();

    Location::factory()
        ->for($organization)
        ->create([
            'type' => LocationType::Warehouse,
        ]);

    $query = ($this->query)()->ofType(
        type: LocationType::Headquarters,
    );

    $warehouseQuery = ($this->query)()->ofType(
        type: LocationType::Warehouse,
    );

    expect($query->exists())
        ->toBeFalse()
        ->and($warehouseQuery->exists())
        ->toBeTrue();

});

test('can query locations by type', function (): void {

    $query = ($this->query)()->ofType(
        type: LocationType::Headquarters,
    );

    expect($query->builder()->toSql())
        ->toContain('select * from "locations" where "type" = ?');

});

test('can query locations by country', function (): void {

    $query = ($this->query)()->ofCountry(
        country: Country::factory()->createQuietly(),
    );

    expect($query->builder()->toSql())
        ->toContain('select * from "locations" where "country_id" = ?');

});

test('can query locations of an organization', function (): void {

    $organization = Organization::factory()
        ->createQuietly();

    $query = ($this->query)()->ofOrganization($organization);

    expect($query->builder()->toSql())
        ->toContain('select * from "locations" where "organization_id" = ?');

});

test('can exclude a location from query', function (): void {

    /** @var Location $location */
    $location = Location::factory()->createQuietly();

    $query = ($this->query)()->except($location);

    expect($query->builder()->toSql())
        ->toContain('select * from "locations" where "id" != ?');

});

test('except excludes the correct location', function (): void {

    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var Location $location1 */
    $location1 = Location::factory()->createQuietly([
        'organization_id' => $organization->id,
        'name' => 'Location 1',
    ]);

    /** @var Location $location2 */
    $location2 = Location::factory()->createQuietly([
        'organization_id' => $organization->id,
        'name' => 'Location 2',
    ]);

    $locations = ($this->query)()
        ->ofOrganization($organization)
        ->except($location1)
        ->get();

    expect($locations)
        ->toHaveCount(1)
        ->and($locations->first()->id)
        ->toBe($location2->id);
});
