<?php

declare(strict_types=1);

use App\Enums\Icon;

test('all cases have string values matching their names', function (): void {
    foreach (Icon::cases() as $case) {
        expect($case->value)->toBe($case->name);
    }
});

test('has travel and vacation icons', function (): void {
    expect(Icon::LucidePlane->value)
        ->toBe('LucidePlane')
        ->and(Icon::LucidePlaneTakeoff->value)
        ->toBe('LucidePlaneTakeoff')
        ->and(Icon::LucideShip->value)
        ->toBe('LucideShip')
        ->and(Icon::LucideCar->value)
        ->toBe('LucideCar')
        ->and(Icon::LucideTreePalm->value)
        ->toBe('LucideTreePalm')
        ->and(Icon::LucideUmbrella->value)
        ->toBe('LucideUmbrella')
        ->and(Icon::LucideSun->value)
        ->toBe('LucideSun')
        ->and(Icon::LucideMountain->value)
        ->toBe('LucideMountain')
        ->and(Icon::LucideTent->value)
        ->toBe('LucideTent')
        ->and(Icon::LucideMap->value)
        ->toBe('LucideMap');
});

test('has medical and health icons', function (): void {
    expect(Icon::LucideHeart->value)
        ->toBe('LucideHeart')
        ->and(Icon::LucideHeartPulse->value)
        ->toBe('LucideHeartPulse')
        ->and(Icon::LucideHospital->value)
        ->toBe('LucideHospital')
        ->and(Icon::LucideStethoscope->value)
        ->toBe('LucideStethoscope')
        ->and(Icon::LucidePill->value)
        ->toBe('LucidePill')
        ->and(Icon::LucideThermometer->value)
        ->toBe('LucideThermometer')
        ->and(Icon::LucideActivity->value)
        ->toBe('LucideActivity')
        ->and(Icon::LucideCross->value)
        ->toBe('LucideCross');
});

test('has family and personal icons', function (): void {
    expect(Icon::LucideBaby->value)
        ->toBe('LucideBaby')
        ->and(Icon::LucideHome->value)
        ->toBe('LucideHome')
        ->and(Icon::LucideHouse->value)
        ->toBe('LucideHouse')
        ->and(Icon::LucideSofa->value)
        ->toBe('LucideSofa')
        ->and(Icon::LucideBed->value)
        ->toBe('LucideBed')
        ->and(Icon::LucideUsers->value)
        ->toBe('LucideUsers')
        ->and(Icon::LucideUserPlus->value)
        ->toBe('LucideUserPlus')
        ->and(Icon::LucideHandHeart->value)
        ->toBe('LucideHandHeart');
});

test('has calendar and time icons', function (): void {
    expect(Icon::LucideCalendar->value)
        ->toBe('LucideCalendar')
        ->and(Icon::LucideCalendarDays->value)
        ->toBe('LucideCalendarDays')
        ->and(Icon::LucideCalendarCheck->value)
        ->toBe('LucideCalendarCheck')
        ->and(Icon::LucideCalendarClock->value)
        ->toBe('LucideCalendarClock')
        ->and(Icon::LucideClock->value)
        ->toBe('LucideClock')
        ->and(Icon::LucideTimer->value)
        ->toBe('LucideTimer')
        ->and(Icon::LucideHourglass->value)
        ->toBe('LucideHourglass');
});
