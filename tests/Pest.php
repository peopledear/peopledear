<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use Tests\TestCase;

function loadTestsDefaults(): void
{
    Str::createRandomStringsNormally();
    Str::createUuidsNormally();
    Http::preventStrayRequests();
    Sleep::fake();
}

pest()->browser()->timeout(20000);

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function (): void {
        loadTestsDefaults();
        $this->freezeTime();
    })
    ->in('Unit', 'Integration');

pest()->extend(Tests\WithUsersTestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function (): void {
        loadTestsDefaults();
        pest()->browser()->withHost('localhost');
        $this->freezeTime();
    })
    ->in('Browser/Landlord');

pest()->extend(Tests\WithUsersTestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function (): void {
        loadTestsDefaults();
        $this->freezeTime();

    })
    ->in('Feature');

pest()->extend(Tests\TenantTestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function (): void {
        pest()->browser()->withHost('acme.localhost');
        loadTestsDefaults();
        $this->freezeTime();
    })
    ->in('Browser/Tenant');

function something(): void {}
