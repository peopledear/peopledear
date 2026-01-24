<?php

declare(strict_types=1);

use App\Enums\RequestStatusIcon;

test('all cases have string values matching their names', function (): void {
    foreach (RequestStatusIcon::cases() as $case) {
        expect($case->value)->toBe($case->name);
    }
});

test('has pending status icons', function (): void {
    expect(RequestStatusIcon::Timer->value)->toBe('Timer')
        ->and(RequestStatusIcon::Clock->value)->toBe('Clock')
        ->and(RequestStatusIcon::Hourglass->value)->toBe('Hourglass')
        ->and(RequestStatusIcon::Loader->value)->toBe('Loader')
        ->and(RequestStatusIcon::CircleDashed->value)->toBe('CircleDashed')
        ->and(RequestStatusIcon::CircleDot->value)->toBe('CircleDot')
        ->and(RequestStatusIcon::Pause->value)->toBe('Pause')
        ->and(RequestStatusIcon::AlertCircle->value)->toBe('AlertCircle');
});

test('has approved status icons', function (): void {
    expect(RequestStatusIcon::Check->value)->toBe('Check')
        ->and(RequestStatusIcon::CheckCircle->value)->toBe('CheckCircle')
        ->and(RequestStatusIcon::CheckCircle2->value)->toBe('CheckCircle2')
        ->and(RequestStatusIcon::CircleCheck->value)->toBe('CircleCheck')
        ->and(RequestStatusIcon::ThumbsUp->value)->toBe('ThumbsUp')
        ->and(RequestStatusIcon::BadgeCheck->value)->toBe('BadgeCheck')
        ->and(RequestStatusIcon::ShieldCheck->value)->toBe('ShieldCheck');
});

test('has rejected status icons', function (): void {
    expect(RequestStatusIcon::X->value)->toBe('X')
        ->and(RequestStatusIcon::XCircle->value)->toBe('XCircle')
        ->and(RequestStatusIcon::CircleX->value)->toBe('CircleX')
        ->and(RequestStatusIcon::CircleOff->value)->toBe('CircleOff')
        ->and(RequestStatusIcon::ThumbsDown->value)->toBe('ThumbsDown')
        ->and(RequestStatusIcon::Ban->value)->toBe('Ban')
        ->and(RequestStatusIcon::ShieldX->value)->toBe('ShieldX')
        ->and(RequestStatusIcon::AlertTriangle->value)->toBe('AlertTriangle');
});

test('has cancelled status icons', function (): void {
    expect(RequestStatusIcon::Slash->value)->toBe('Slash')
        ->and(RequestStatusIcon::MinusCircle->value)->toBe('MinusCircle')
        ->and(RequestStatusIcon::CircleMinus->value)->toBe('CircleMinus')
        ->and(RequestStatusIcon::Square->value)->toBe('Square')
        ->and(RequestStatusIcon::StopCircle->value)->toBe('StopCircle')
        ->and(RequestStatusIcon::Trash2->value)->toBe('Trash2')
        ->and(RequestStatusIcon::Undo2->value)->toBe('Undo2');
});

test('grouped method returns all categories', function (): void {
    $grouped = RequestStatusIcon::grouped();

    expect($grouped)
        ->toBeArray()
        ->toHaveKeys([
            'Pending / Waiting',
            'Approved / Success',
            'Rejected / Error',
            'Cancelled / Stopped',
            'In Progress / Processing',
            'On Hold / Paused',
            'Review / Needs Attention',
        ]);
});

test('grouped method contains correct icons per category', function (): void {
    $grouped = RequestStatusIcon::grouped();

    expect($grouped['Pending / Waiting'])
        ->toHaveKey('Timer')
        ->toHaveKey('Clock')
        ->and($grouped['Approved / Success'])
        ->toHaveKey('CheckCircle')
        ->toHaveKey('ThumbsUp')
        ->and($grouped['Rejected / Error'])
        ->toHaveKey('XCircle')
        ->toHaveKey('CircleOff');
});

test('values method returns all icon values', function (): void {
    $values = RequestStatusIcon::values();

    expect($values)
        ->toBeArray()
        ->toContain('Timer')
        ->toContain('CheckCircle')
        ->toContain('XCircle')
        ->toContain('Slash');
});
