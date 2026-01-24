<?php

declare(strict_types=1);

use App\Data\PeopleDear\TimeOffTypeStatus\TimeOffTypeStatusData;
use App\Enums\TimeOffTypeStatus;

test('creates from array',
    /**
     * @throws Throwable
     */
    function (): void {

        $data = TimeOffTypeStatusData::from([
            'value' => TimeOffTypeStatus::Active->value,
            'label' => 'Active',
            'color' => 'success',
        ]);

        expect($data)
            ->toBeInstanceOf(TimeOffTypeStatusData::class)
            ->and($data->value)
            ->toBe(TimeOffTypeStatus::Active->value)
            ->and($data->label)
            ->toBe('Active')
            ->and($data->color)
            ->toBe('success');

    });

test('creates for each status',
    /**
     * @throws Throwable
     */
    function (TimeOffTypeStatus $status, string $expectedLabel, string $expectedColor): void {

        $data = TimeOffTypeStatusData::from([
            'value' => $status->value,
            'label' => $expectedLabel,
            'color' => $expectedColor,
        ]);

        expect($data->value)
            ->toBe($status->value)
            ->and($data->label)
            ->toBe($expectedLabel)
            ->and($data->color)
            ->toBe($expectedColor);

    })->with([
        [TimeOffTypeStatus::Pending, 'Pending', 'warning'],
        [TimeOffTypeStatus::Active, 'Active', 'success'],
        [TimeOffTypeStatus::Inactive, 'Inactive', 'secondary'],
    ]);
