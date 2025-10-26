<?php

declare(strict_types=1);

use App\Http\Integrations\OpenHolidays\Data\HolidayData;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Config;

test('holiday data can be created with all fields', function (): void {
    $data = new HolidayData(
        id: 'uuid-123',
        startDate: CarbonImmutable::parse('2025-12-25'),
        endDate: CarbonImmutable::parse('2025-12-25'),
        name: ['en' => 'Christmas Day', 'pt' => 'Dia de Natal'],
        nationwide: true,
        type: ['Public'],
        subdivisions: null,
        comment: null,
    );

    expect($data->id)
        ->toBe('uuid-123')
        ->and($data->startDate->format('Y-m-d'))
        ->toBe('2025-12-25')
        ->and($data->endDate->format('Y-m-d'))
        ->toBe('2025-12-25')
        ->and($data->name)
        ->toBe(['en' => 'Christmas Day', 'pt' => 'Dia de Natal'])
        ->and($data->nationwide)
        ->toBeTrue()
        ->and($data->type)
        ->toBe(['Public'])
        ->and($data->subdivisions)
        ->toBeNull()
        ->and($data->comment)
        ->toBeNull();
});

test('holiday data can be created with subdivisions', function (): void {
    $data = new HolidayData(
        id: 'uuid-456',
        startDate: CarbonImmutable::parse('2025-07-04'),
        endDate: CarbonImmutable::parse('2025-07-04'),
        name: ['en' => 'Independence Day'],
        nationwide: false,
        type: ['Regional'],
        subdivisions: [['code' => 'CA', 'name' => 'California']],
        comment: 'Regional holiday',
    );

    expect($data->subdivisions)
        ->not->toBeNull()
        ->toBeArray()
        ->toHaveCount(1);
});

test('is included type returns true for included types', function (): void {
    Config::set('openholidays.included_types', ['Public', 'Regional']);

    $data = new HolidayData(
        id: 'uuid-123',
        startDate: CarbonImmutable::parse('2025-12-25'),
        endDate: CarbonImmutable::parse('2025-12-25'),
        name: ['en' => 'Christmas Day'],
        nationwide: true,
        type: ['Public'],
    );

    expect($data->isIncludedType())->toBeTrue();
});

test('is included type returns true when any type matches', function (): void {
    Config::set('openholidays.included_types', ['Public', 'Regional']);

    $data = new HolidayData(
        id: 'uuid-123',
        startDate: CarbonImmutable::parse('2025-12-25'),
        endDate: CarbonImmutable::parse('2025-12-25'),
        name: ['en' => 'Holiday'],
        nationwide: true,
        type: ['School', 'Regional', 'Bank'],
    );

    expect($data->isIncludedType())->toBeTrue();
});

test('is included type returns false for excluded types', function (): void {
    Config::set('openholidays.included_types', ['Public', 'Regional']);

    $data = new HolidayData(
        id: 'uuid-123',
        startDate: CarbonImmutable::parse('2025-12-25'),
        endDate: CarbonImmutable::parse('2025-12-25'),
        name: ['en' => 'Bank Holiday'],
        nationwide: true,
        type: ['Bank'],
    );

    expect($data->isIncludedType())->toBeFalse();
});

test('is included type returns false for empty types', function (): void {
    Config::set('openholidays.included_types', ['Public']);

    $data = new HolidayData(
        id: 'uuid-123',
        startDate: CarbonImmutable::parse('2025-12-25'),
        endDate: CarbonImmutable::parse('2025-12-25'),
        name: ['en' => 'Holiday'],
        nationwide: true,
        type: [],
    );

    expect($data->isIncludedType())->toBeFalse();
});

test('get localized name returns specified language', function (): void {
    $data = new HolidayData(
        id: 'uuid-123',
        startDate: CarbonImmutable::parse('2025-12-25'),
        endDate: CarbonImmutable::parse('2025-12-25'),
        name: ['en' => 'Christmas Day', 'pt' => 'Dia de Natal', 'es' => 'Navidad'],
        nationwide: true,
        type: ['Public'],
    );

    expect($data->getLocalizedName('pt'))
        ->toBe('Dia de Natal')
        ->and($data->getLocalizedName('es'))
        ->toBe('Navidad')
        ->and($data->getLocalizedName('en'))
        ->toBe('Christmas Day');
});

test('get localized name falls back to en when language not found', function (): void {
    $data = new HolidayData(
        id: 'uuid-123',
        startDate: CarbonImmutable::parse('2025-12-25'),
        endDate: CarbonImmutable::parse('2025-12-25'),
        name: ['en' => 'Christmas Day', 'pt' => 'Dia de Natal'],
        nationwide: true,
        type: ['Public'],
    );

    expect($data->getLocalizedName('fr'))->toBe('Christmas Day');
});

test('get localized name uses default language from config', function (): void {
    Config::set('openholidays.default_language', 'pt');

    $data = new HolidayData(
        id: 'uuid-123',
        startDate: CarbonImmutable::parse('2025-12-25'),
        endDate: CarbonImmutable::parse('2025-12-25'),
        name: ['en' => 'Christmas Day', 'pt' => 'Dia de Natal'],
        nationwide: true,
        type: ['Public'],
    );

    expect($data->getLocalizedName())->toBe('Dia de Natal');
});

test('get localized name falls back to first available when no match', function (): void {
    $data = new HolidayData(
        id: 'uuid-123',
        startDate: CarbonImmutable::parse('2025-12-25'),
        endDate: CarbonImmutable::parse('2025-12-25'),
        name: ['de' => 'Weihnachten', 'fr' => 'Noël'],
        nationwide: true,
        type: ['Public'],
    );

    expect($data->getLocalizedName('en'))->toBe('Weihnachten');
});

test('get localized name returns unknown holiday when empty', function (): void {
    $data = new HolidayData(
        id: 'uuid-123',
        startDate: CarbonImmutable::parse('2025-12-25'),
        endDate: CarbonImmutable::parse('2025-12-25'),
        name: [],
        nationwide: true,
        type: ['Public'],
    );

    expect($data->getLocalizedName())->toBe('Unknown Holiday');
});
