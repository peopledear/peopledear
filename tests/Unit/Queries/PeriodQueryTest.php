<?php

declare(strict_types=1);

use App\Enums\Support\SessionKey;
use App\Models\Organization;
use App\Models\Period;
use App\Queries\PeriodQuery;
use Illuminate\Support\Facades\Session;

beforeEach(function (): void {

    $this->organization = Organization::factory()
        ->createQuietly();

    Session::put(SessionKey::CurrentOrganization->value, $this->organization->id);

    $this->activePeriod = Period::factory()
        ->for($this->organization)
        ->active()
        ->createQuietly();

    $this->organization2 = Organization::factory()
        ->createQuietly();

    $this->activePeriod2 = Period::factory()
        ->for($this->organization2)
        ->active()
        ->createQuietly();

    $this->query = app(PeriodQuery::class);

});

test('period is active', function (): void {

    $result = $this->query->active()->first();

    $periods = $this->query->builder()->get();

    expect($result)->toBeInstanceOf(Period::class)
        ->and($result->id)
        ->toBe($this->activePeriod->id)
        ->and($result->id)
        ->not
        ->toBe($this->activePeriod2->id)
        ->and($periods)
        ->toHaveCount(1);

});
