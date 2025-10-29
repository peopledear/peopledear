<?php

declare(strict_types=1);

use App\Models\Country;

test('command has correct signature', function (): void {
    $this->artisan('app:install')
        ->assertSuccessful();
});

test('command seeds countries when none exist', function (): void {
    Country::query()->delete();

    expect(Country::query()->count())->toBe(0);

    $this->artisan('app:install')
        ->assertSuccessful();

    expect(Country::query()->count())->toBe(36);
});

test('command is idempotent using upsert', function (): void {
    Country::query()->delete();

    $this->artisan('app:install')
        ->assertSuccessful();

    $initialCount = Country::query()->count();

    expect($initialCount)->toBe(36);

    $this->artisan('app:install')
        ->assertSuccessful();

    expect(Country::query()->count())->toBe(36);
});

test('command updates existing countries', function (): void {
    Country::query()->delete();

    $this->artisan('app:install')
        ->assertSuccessful();

    /** @var Country $portugal */
    $portugal = Country::query()
        ->where('iso_code', 'PT')
        ->first();

    expect($portugal)->not->toBeNull();

    $originalName = $portugal->name;

    Country::query()
        ->where('iso_code', 'PT')
        ->update(['name' => json_encode(['en' => 'Modified Portugal'])]);

    /** @var Country $modified */
    $modified = Country::query()
        ->where('iso_code', 'PT')
        ->first()
        ?->fresh();

    expect($modified->name)->toBe(['en' => 'Modified Portugal']);

    $this->artisan('app:install')
        ->assertSuccessful();

    /** @var Country $restored */
    $restored = Country::query()
        ->where('iso_code', 'PT')
        ->first()
        ?->fresh();

    expect($restored->name)->toBe($originalName);
});

test('command returns success exit code', function (): void {
    $this->artisan('app:install')
        ->assertExitCode(0);
});
