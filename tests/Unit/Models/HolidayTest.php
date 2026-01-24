<?php

declare(strict_types=1);

use App\Enums\HolidayType;
use App\Models\Holiday;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('holiday has organization relationship', function (): void {
    /** @var Holiday $holiday */
    $holiday = Holiday::factory()->create();

    expect($holiday->organization())
        ->toBeInstanceOf(BelongsTo::class);
});

test('holiday organization relationship is properly loaded', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->create();

    /** @var Holiday $holiday */
    $holiday = Holiday::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $holiday->load('organization');

    expect($holiday->organization)
        ->toBeInstanceOf(Organization::class)
        ->and($holiday->organization->id)
        ->toBe($organization->id);
});

test('holiday type is cast to HolidayType enum', function (): void {
    /** @var Holiday $holiday */
    $holiday = Holiday::factory()->create([
        'type' => HolidayType::Public,
    ]);

    expect($holiday->type)
        ->toBeInstanceOf(HolidayType::class)
        ->and($holiday->type)
        ->toBe(HolidayType::Public)
        ->and($holiday->type->value)
        ->toBe(1);
});

test('holiday date is cast to date', function (): void {
    /** @var Holiday $holiday */
    $holiday = Holiday::factory()->create([
        'date' => '2025-12-25',
    ]);

    expect($holiday->date)
        ->toBeInstanceOf(Carbon\CarbonInterface::class)
        ->and($holiday->date->format('Y-m-d'))
        ->toBe('2025-12-25');
});

test('holiday name is cast to array', function (): void {
    /** @var Holiday $holiday */
    $holiday = Holiday::factory()->create([
        'name' => [
            'en' => 'Christmas Day',
            'pt' => 'Dia de Natal',
        ],
    ]);

    expect($holiday->name)
        ->toBeArray()
        ->and($holiday->name['en'])
        ->toBe('Christmas Day')
        ->and($holiday->name['pt'])
        ->toBe('Dia de Natal');
});

test('holiday nationwide is cast to boolean', function (): void {
    /** @var Holiday $holiday */
    $holiday = Holiday::factory()->create([
        'nationwide' => true,
    ]);

    expect($holiday->nationwide)
        ->toBeTrue();
});

test('holiday is_custom is cast to boolean', function (): void {
    /** @var Holiday $holiday */
    $holiday = Holiday::factory()->custom()->create();

    expect($holiday->is_custom)
        ->toBeTrue();
});

test('holiday can be created from api with api_holiday_id', function (): void {
    /** @var Holiday $holiday */
    $holiday = Holiday::factory()->fromApi()->create();

    expect($holiday->is_custom)
        ->toBeFalse()
        ->and($holiday->api_holiday_id)
        ->not->toBeNull();
});

test('holiday can be nationwide without subdivision', function (): void {
    /** @var Holiday $holiday */
    $holiday = Holiday::factory()->nationwide()->create();

    expect($holiday->nationwide)
        ->toBeTrue()
        ->and($holiday->subdivision_code)
        ->toBeNull();
});

test('holiday can be regional with subdivision', function (): void {
    /** @var Holiday $holiday */
    $holiday = Holiday::factory()->regional()->create();

    expect($holiday->nationwide)
        ->toBeFalse()
        ->and($holiday->subdivision_code)
        ->not->toBeNull();
});

test('holiday unique constraint on organization and api_holiday_id', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->create();

    /** @var Holiday $holiday */
    $holiday = Holiday::factory()->create([
        'organization_id' => $organization->id,
        'api_holiday_id' => 'test-uuid-123',
    ]);

    expect(fn () => Holiday::factory()->create([
        'organization_id' => $organization->id,
        'api_holiday_id' => 'test-uuid-123',
    ]))
        ->toThrow(Illuminate\Database\QueryException::class);
});

test('holiday subdivision_code can be null', function (): void {
    /** @var Holiday $holiday */
    $holiday = Holiday::factory()->create([
        'subdivision_code' => null,
    ]);

    expect($holiday->subdivision_code)->toBeNull();
});

test('holiday api_holiday_id can be null for custom holidays', function (): void {
    /** @var Holiday $holiday */
    $holiday = Holiday::factory()->custom()->create();

    expect($holiday->api_holiday_id)->toBeNull();
});

test('holiday has country relationship', function (): void {
    /** @var Holiday $holiday */
    $holiday = Holiday::factory()->create();

    expect($holiday->country())->toBeInstanceOf(BelongsTo::class);
});

test('holiday country relationship is properly loaded', function (): void {
    /** @var App\Models\Country $country */
    $country = App\Models\Country::factory()->create([
        'iso_code' => 'XX',
        'name' => ['en' => 'Test Country'],
        'official_languages' => ['en'],
    ]);

    /** @var Holiday $holiday */
    $holiday = Holiday::factory()->create([
        'country_id' => $country->id,
    ]);

    $holiday->load('country');

    expect($holiday->country)
        ->toBeInstanceOf(App\Models\Country::class)
        ->and($holiday->country->iso_code)
        ->toBe('XX');
});

test('to array', function (): void {
    /** @var Holiday $holiday */
    $holiday = Holiday::factory()
        ->create()
        ->refresh();

    expect(array_keys($holiday->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'organization_id',
            'country_id',
            'date',
            'name',
            'type',
            'nationwide',
            'subdivision_code',
            'api_holiday_id',
            'is_custom',
        ]);
});
