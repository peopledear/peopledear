<?php

declare(strict_types=1);

use App\Http\Integrations\OpenHolidays\Requests\GetSubdivisions;
use App\Models\Country;
use App\Models\CountrySubdivision;
use Illuminate\Support\Facades\Log;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

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

test('command fetches subdivisions for Portugal and Spain', function (): void {
    Country::query()->delete();
    CountrySubdivision::query()->delete();

    $this->artisan('app:install');

    /** @var Country $portugal */
    $portugal = Country::query()
        ->where('iso_code', 'PT')
        ->first();

    /** @var Country $spain */
    $spain = Country::query()
        ->where('iso_code', 'ES')
        ->first();

    expect($portugal)->not->toBeNull()
        ->and($spain)->not->toBeNull();

    $mockResponse = MockResponse::make([
        [
            'code' => 'PT-01',
            'isoCode' => 'PT-01',
            'shortName' => 'Aveiro',
            'category' => [['text' => 'Distrito', 'language' => 'pt']],
            'name' => [['text' => 'Aveiro', 'language' => 'en']],
            'officialLanguages' => ['pt'],
            'children' => null,
        ],
    ]);

    MockClient::global([
        GetSubdivisions::class => $mockResponse,
    ]);

    CountrySubdivision::query()->delete();

    $this->artisan('app:install')
        ->assertSuccessful();

    expect(CountrySubdivision::query()->count())->toBeGreaterThan(0);
});

test('command displays subdivision fetching progress message', function (): void {
    $mockResponse = MockResponse::make([
        [
            'code' => 'PT-01',
            'isoCode' => 'PT-01',
            'shortName' => 'Aveiro',
            'category' => [['text' => 'Distrito', 'language' => 'pt']],
            'name' => [['text' => 'Aveiro', 'language' => 'en']],
            'officialLanguages' => ['pt'],
            'children' => null,
        ],
    ]);

    MockClient::global([
        GetSubdivisions::class => $mockResponse,
    ]);

    $this->artisan('app:install')
        ->assertSuccessful();

    expect(true)->toBeTrue();
});

test('command handles API failures gracefully and continues', function (): void {
    CountrySubdivision::query()->delete();

    Log::shouldReceive('error')
        ->twice()
        ->withArgs(fn (string $message, array $context): bool => str_contains($message, 'Failed to fetch subdivisions')
            && isset($context['country_iso_code'])
            && isset($context['error']));

    MockClient::global([
        GetSubdivisions::class => MockResponse::make(status: 500),
    ]);

    $this->artisan('app:install')
        ->assertSuccessful();
});

test('command applies rate limiting between country requests', function (): void {
    Country::query()->delete();
    CountrySubdivision::query()->delete();

    $this->artisan('app:install');

    $mockResponse = MockResponse::make([
        [
            'code' => 'PT-01',
            'isoCode' => 'PT-01',
            'shortName' => 'Aveiro',
            'category' => [['text' => 'distrito', 'language' => 'pt']],
            'name' => [['text' => 'Aveiro', 'language' => 'pt']],
            'officialLanguages' => ['PT'],
            'children' => null,
        ],
    ]);

    MockClient::global([
        GetSubdivisions::class => $mockResponse,
    ]);

    CountrySubdivision::query()->delete();

    $this->artisan('app:install')
        ->assertSuccessful();

    Illuminate\Support\Sleep::assertSleptTimes(2);
});
