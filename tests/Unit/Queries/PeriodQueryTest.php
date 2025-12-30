<?php

declare(strict_types=1);

use App\Enums\Support\SessionKey;
use App\Models\Organization;
use App\Models\Period;
use App\Queries\PeriodQuery;
use Illuminate\Support\Facades\Session;

beforeEach(function (): void {

    /** @var Organization $organization */
    $organization = Organization::factory()
        ->createQuietly();

    $this->organization = $organization;

    Session::put(SessionKey::CurrentOrganization->value, $this->organization->id);

    /** @var Period $activePeriod */
    $activePeriod = Period::factory()
        ->for($this->organization)
        ->active()
        ->createQuietly();

    $this->activePeriod = $activePeriod;

    /** @var Organization $organization2 */
    $organization2 = Organization::factory()
        ->createQuietly();

    $this->organization2 = $organization2;

    /** @var Period $activePeriod2 */
    $activePeriod2 = Period::factory()
        ->for($this->organization2)
        ->active()
        ->createQuietly();

    $this->activePeriod2 = $activePeriod2;

    $this->query = resolve(PeriodQuery::class);

});

test('returns only active periods for current organization', function (): void {

    /** @var PeriodQuery $query */
    $query = $this->query;

    /** @var Period $result */
    $result = $query()->active()->first();

    $periods = $query()->active()->builder()->get();

    expect($result)
        ->toBeInstanceOf(Period::class)
        ->id->toBe($this->activePeriod->id)
        ->id->not->toBe($this->activePeriod2->id)
        ->and($periods)
        ->toHaveCount(1);

});

test('filters by id when passed to invoke', function (): void {

    /** @var PeriodQuery $query */
    $query = $this->query;

    /** @var Period $result */
    $result = $query($this->activePeriod->id)->first();

    expect($result)
        ->toBeInstanceOf(Period::class)
        ->id->toBe($this->activePeriod->id);

});
