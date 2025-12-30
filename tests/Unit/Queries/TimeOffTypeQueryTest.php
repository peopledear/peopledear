<?php

declare(strict_types=1);

use App\Enums\Support\SessionKey;
use App\Enums\TimeOffTypeStatus;
use App\Models\Organization;
use App\Models\TimeOffType;
use App\Queries\TimeOffTypeQuery;
use Illuminate\Support\Facades\Session;

test('selects from the correct table', function (): void {

    $query = new TimeOffTypeQuery;

    $sql = $query()
        ->builder()
        ->toRawSql();

    expect($sql)
        ->toContain(sprintf('from "%s"', (new TimeOffType)->getTable()));

});

test('scopes by active status', function (): void {

    $query = new TimeOffTypeQuery;

    $sql = $query()
        ->active()
        ->builder()
        ->toRawSql();

    expect($sql)
        ->toContain('"status" = 2'); // TimeOffTypeStatus::Active has value 2

});

test('returns only active time off types for current organization', function (): void {

    /** @var Organization $organization */
    $organization = Organization::factory()
        ->createQuietly();

    Session::put(SessionKey::CurrentOrganization->value, $organization->id);

    /** @var TimeOffType $activeType */
    $activeType = TimeOffType::factory()
        ->for($organization)
        ->active()
        ->createQuietly();

    /** @var TimeOffType $inactiveType */
    $inactiveType = TimeOffType::factory()
        ->for($organization)
        ->inactive()
        ->createQuietly();

    /** @var Organization $otherOrganization */
    $otherOrganization = Organization::factory()
        ->createQuietly();

    /** @var TimeOffType $otherOrgType */
    $otherOrgType = TimeOffType::factory()
        ->for($otherOrganization)
        ->active()
        ->createQuietly();

    /** @var TimeOffTypeQuery $query */
    $query = resolve(TimeOffTypeQuery::class);

    $results = $query()->active()->builder()->get();

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
        ->active()
        ->createQuietly();

    /** @var TimeOffType $inactiveType */
    $inactiveType = TimeOffType::factory()
        ->for($organization)
        ->inactive()
        ->createQuietly();

    /** @var TimeOffTypeQuery $query */
    $query = resolve(TimeOffTypeQuery::class);

    $results = $query()->builder()->get();

    expect($results)
        ->toHaveCount(2);

});
