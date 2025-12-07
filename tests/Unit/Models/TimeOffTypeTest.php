<?php

declare(strict_types=1);

use App\Enums\PeopleDear\TimeOffUnit;
use App\Models\TimeOffType;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;

test('fall back approval role relationship', function (): void {

    $timeOffType = TimeOffType::factory()
        ->withFallbackApprovalRole()
        ->create()
        ->fresh();

    expect($timeOffType->fallbackApprovalRole)
        ->toBeInstanceOf(Role::class)
        ->and($timeOffType->fallbackApprovalRole->id)
        ->toBe($timeOffType->fallback_approval_role_id);

});

test('allowed units is an array of time off unit enums', function (): void {

    $timeOffType = TimeOffType::factory()
        ->withFallbackApprovalRole()
        ->create([
            'allowed_units' => [
                TimeOffUnit::Day,
                TimeOffUnit::Hour,
            ],
        ])
        ->fresh();

    expect($timeOffType->allowed_units)
        ->toBeArray()
        ->each->toBeInstanceOf(TimeOffUnit::class)
        ->and(Arr::first($timeOffType->allowed_units))
        ->toBe(TimeOffUnit::Day)
        ->and(Arr::last($timeOffType->allowed_units))
        ->toBe(TimeOffUnit::Hour);

});

test('to array', function (): void {

    $timeOffType = TimeOffType::factory()
        ->withFallbackApprovalRole()
        ->create()
        ->fresh();

    expect(array_keys($timeOffType->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'organization_id',
            'fallback_approval_role_id',
            'name',
            'description',
            'is_system',
            'allowed_units',
            'icon',
            'color',
            'is_active',
            'requires_approval',
            'requires_justification',
            'requires_justification_document',
            'balance_mode',
        ]);

});
