<?php

declare(strict_types=1);

use App\Enums\Support\TimeOffIcon;

test('all cases have string values matching their names', function (): void {
    foreach (TimeOffIcon::cases() as $case) {
        expect($case->value)->toBe($case->name);
    }
});

test('has travel and vacation icons', function (): void {
    expect(TimeOffIcon::Plane->value)->toBe('Plane')
        ->and(TimeOffIcon::PlaneTakeoff->value)->toBe('PlaneTakeoff')
        ->and(TimeOffIcon::Ship->value)->toBe('Ship')
        ->and(TimeOffIcon::Car->value)->toBe('Car')
        ->and(TimeOffIcon::TreePalm->value)->toBe('TreePalm')
        ->and(TimeOffIcon::Umbrella->value)->toBe('Umbrella')
        ->and(TimeOffIcon::Sun->value)->toBe('Sun')
        ->and(TimeOffIcon::Mountain->value)->toBe('Mountain')
        ->and(TimeOffIcon::Tent->value)->toBe('Tent')
        ->and(TimeOffIcon::Map->value)->toBe('Map');
});

test('has medical and health icons', function (): void {
    expect(TimeOffIcon::Heart->value)->toBe('Heart')
        ->and(TimeOffIcon::HeartPulse->value)->toBe('HeartPulse')
        ->and(TimeOffIcon::Hospital->value)->toBe('Hospital')
        ->and(TimeOffIcon::Stethoscope->value)->toBe('Stethoscope')
        ->and(TimeOffIcon::Pill->value)->toBe('Pill')
        ->and(TimeOffIcon::Thermometer->value)->toBe('Thermometer')
        ->and(TimeOffIcon::Activity->value)->toBe('Activity')
        ->and(TimeOffIcon::Cross->value)->toBe('Cross');
});

test('has family and personal icons', function (): void {
    expect(TimeOffIcon::Baby->value)->toBe('Baby')
        ->and(TimeOffIcon::Home->value)->toBe('Home')
        ->and(TimeOffIcon::House->value)->toBe('House')
        ->and(TimeOffIcon::Sofa->value)->toBe('Sofa')
        ->and(TimeOffIcon::Bed->value)->toBe('Bed')
        ->and(TimeOffIcon::Users->value)->toBe('Users')
        ->and(TimeOffIcon::UserPlus->value)->toBe('UserPlus')
        ->and(TimeOffIcon::HandHeart->value)->toBe('HandHeart');
});

test('has calendar and time icons', function (): void {
    expect(TimeOffIcon::Calendar->value)->toBe('Calendar')
        ->and(TimeOffIcon::CalendarDays->value)->toBe('CalendarDays')
        ->and(TimeOffIcon::CalendarCheck->value)->toBe('CalendarCheck')
        ->and(TimeOffIcon::CalendarClock->value)->toBe('CalendarClock')
        ->and(TimeOffIcon::Clock->value)->toBe('Clock')
        ->and(TimeOffIcon::Timer->value)->toBe('Timer')
        ->and(TimeOffIcon::Hourglass->value)->toBe('Hourglass');
});

test('grouped method returns all categories', function (): void {
    $grouped = TimeOffIcon::grouped();

    expect($grouped)
        ->toBeArray()
        ->toHaveKeys([
            'Travel & Vacation',
            'Medical & Health',
            'Family & Personal',
            'Calendar & Time',
            'Education & Training',
            'Work & Business',
            'Celebration & Events',
            'Mourning & Bereavement',
            'Other',
        ]);
});

test('grouped method contains correct icons per category', function (): void {
    $grouped = TimeOffIcon::grouped();

    expect($grouped['Travel & Vacation'])
        ->toHaveKey('Plane')
        ->toHaveKey('Sun')
        ->and($grouped['Medical & Health'])
        ->toHaveKey('Heart')
        ->toHaveKey('Hospital')
        ->and($grouped['Family & Personal'])
        ->toHaveKey('Baby')
        ->toHaveKey('Home');
});

test('values method returns all icon values', function (): void {
    $values = TimeOffIcon::values();

    expect($values)
        ->toBeArray()
        ->toContain('Plane')
        ->toContain('Heart')
        ->toContain('Calendar')
        ->toContain('GraduationCap');
});
