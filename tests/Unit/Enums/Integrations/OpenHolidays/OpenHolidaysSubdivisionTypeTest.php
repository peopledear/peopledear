<?php

declare(strict_types=1);

use App\Enums\Integrations\OpenHolidays\OpenHolidaysSubdivisionType;
use App\Enums\PeopleDear\CountrySubdivisionType;

test('distrito maps to District type', function (): void {
    /** @var OpenHolidaysSubdivisionType $type */
    $type = OpenHolidaysSubdivisionType::District;

    expect($type->transform())
        ->toBe(CountrySubdivisionType::District);
});

test('município maps to Municipality type', function (): void {
    /** @var OpenHolidaysSubdivisionType $type */
    $type = OpenHolidaysSubdivisionType::Municipality;

    expect($type->transform())
        ->toBe(CountrySubdivisionType::Municipality);
});

test('região autónoma maps to AutonomousRegion type', function (): void {
    /** @var OpenHolidaysSubdivisionType $type */
    $type = OpenHolidaysSubdivisionType::AutonomousRegion;

    expect($type->transform())
        ->toBe(CountrySubdivisionType::AutonomousRegion);
});

test('provincia maps to Province type', function (): void {
    /** @var OpenHolidaysSubdivisionType $type */
    $type = OpenHolidaysSubdivisionType::Province;

    expect($type->transform())
        ->toBe(CountrySubdivisionType::Province);
});

test('Comunidad autónoma maps to AutonomousRegion type', function (): void {
    /** @var OpenHolidaysSubdivisionType $type */
    $type = OpenHolidaysSubdivisionType::AutonomousCommunity;

    expect($type->transform())
        ->toBe(CountrySubdivisionType::AutonomousRegion);
});

test('Ciudad autónoma del norte de África maps to City type', function (): void {
    /** @var OpenHolidaysSubdivisionType $type */
    $type = OpenHolidaysSubdivisionType::AutonomousCity;

    expect($type->transform())
        ->toBe(CountrySubdivisionType::City);
});

test('Comunidad de Madrid maps to Community type', function (): void {
    /** @var OpenHolidaysSubdivisionType $type */
    $type = OpenHolidaysSubdivisionType::Community;

    expect($type->transform())
        ->toBe(CountrySubdivisionType::Community);
});
