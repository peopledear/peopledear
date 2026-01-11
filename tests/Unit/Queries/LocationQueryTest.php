<?php

declare(strict_types=1);

use App\Enums\PeopleDear\LocationType;
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
