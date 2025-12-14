<?php

declare(strict_types=1);

use App\Enums\Support\SessionKey;
use App\Models\Organization;
use App\Models\TimeOffType;
use App\Queries\TimeOffTypeQuery;
use Illuminate\Support\Facades\Session;

test('selects from the correct table', function (): void {

    $query = new TimeOffTypeQuery;

    $sql = $query()
        ->make()
        ->toRawSql();

    expect($sql)
        ->toContain(sprintf('from "%s"', (new TimeOffType)->getTable()));

});

test('scopes by active status', function (): void {

    $query = new TimeOffTypeQuery;

    $sql = $query()
        ->active()
        ->make()
        ->toRawSql();

    expect($sql)
        ->toContain('"is_active" = 1');

});

test('returns only active time off types for current organization', function (): void {

    /** @var Organization $organization */
    $organization = Organization::factory()
        ->createQuietly();

    Session::put(SessionKey::CurrentOrganization->value, $organization->id);

    /** @var TimeOffType $activeType */
    $activeType = TimeOffType::factory()
        ->for($organization)
        ->createQuietly(['is_active' => true]);

    /** @var TimeOffType $inactiveType */
    $inactiveType = TimeOffType::factory()
        ->for($organization)
        ->createQuietly(['is_active' => false]);

    /** @var Organization $otherOrganization */
    $otherOrganization = Organization::factory()
        ->createQuietly();

    /** @var TimeOffType $otherOrgType */
    $otherOrgType = TimeOffType::factory()
        ->for($otherOrganization)
        ->createQuietly(['is_active' => true]);

    /** @var TimeOffTypeQuery $query */
    $query = app(TimeOffTypeQuery::class);

    $results = $query()->active()->make()->get();

    expect($results)
        ->toHaveCount(1)
        ->first()->id->toBe($activeType->id);

});

test('returns all time off types for current organization when not filtering by active', function (): void {

    /** @var Organization $organization */
    $organization = Organization::factory()
        ->createQuietly();

    Session::put(SessionKey::CurrentOrganization->value, $organization->id);

    /** @var TimeOffType $activeType */
    $activeType = TimeOffType::factory()
        ->for($organization)
        ->createQuietly(['is_active' => true]);

    /** @var TimeOffType $inactiveType */
    $inactiveType = TimeOffType::factory()
        ->for($organization)
        ->createQuietly(['is_active' => false]);

    /** @var TimeOffTypeQuery $query */
    $query = app(TimeOffTypeQuery::class);

    $results = $query()->make()->get();

    expect($results)
        ->toHaveCount(2);

});
