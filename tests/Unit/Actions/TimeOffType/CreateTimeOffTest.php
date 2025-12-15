<?php

declare(strict_types=1);

use App\Actions\TymeOffType\CreateTimeOffType;
use App\Data\PeopleDear\TimeOffType\CreateTimeOffTypeData;
use App\Data\PeopleDear\TimeOffType\TimeOffTypeBalanceConfigData;
use App\Enums\PeopleDear\CarryOverType;
use App\Enums\PeopleDear\TimeOffBalanceMode;
use App\Enums\PeopleDear\TimeOffUnit;
use App\Enums\Support\TimeOffIcon;
use App\Models\Organization;
use App\Models\TimeOffType;
use Spatie\LaravelData\Optional;

beforeEach(function (): void {

    $this->organization = Organization::factory()
        ->create();

    $this->action = resolve(CreateTimeOffType::class);

});

test('create a time off with nullable balance config',
    /**
     * @throws Throwable
     */
    function (): void {

        $sickLeaveData = new CreateTimeOffTypeData(
            name: 'Sick Leave',
            isSystem: true,
            allowedUnits: [
                TimeOffUnit::Day,
                TimeOffUnit::HalfDay,
                TimeOffUnit::Hour,
            ],
            icon: TimeOffIcon::HeartPulse,
            color: '#FF0000',
            isActive: true,
            requiresApproval: false,
            requiresJustification: true,
            requiresJustificationDocument: true,
            balanceMode: TimeOffBalanceMode::None,
            balanceConfig: Optional::create(),
            description: 'Sick leave for employees'
        );

        $sickLeaveTimeOffType = $this->action->handle(
            $this->organization,
            $sickLeaveData
        );

        expect($sickLeaveTimeOffType)
            ->toBeInstanceOf(TimeOffType::class)
            ->name
            ->toBe('Sick Leave');

    });

test('created a time off type',
    /**
     * @throws Throwable
     */
    function (): void {

        $timeOffType = $this->action->handle(
            $this->organization,
            new CreateTimeOffTypeData(
                name: 'Vacation',
                isSystem: true,
                allowedUnits: [
                    TimeOffUnit::Day,
                    TimeOffUnit::HalfDay,
                ],
                icon: TimeOffIcon::PlaneTakeoff,
                color: '#FF5733',
                isActive: true,
                requiresApproval: true,
                requiresJustification: false,
                requiresJustificationDocument: false,
                balanceMode: TimeOffBalanceMode::Annual,
                balanceConfig: TimeOffTypeBalanceConfigData::from([
                    'accrualDaysPerYear' => 22,
                    'carryOverType' => CarryOverType::Limited,
                    'carryOverDaysLimit' => 5,
                ]),
                description: 'Employee vacation time',
            )
        );

        $timeOffTypeFromStorage = TimeOffType::query()
            ->find($timeOffType->id);

        expect($timeOffType)
            ->toBeInstanceOf(TimeOffType::class)
            ->except($timeOffTypeFromStorage)
            ->organization_id
            ->toBe($this->organization->id)
            ->and($timeOffType->description)
            ->toBe($timeOffTypeFromStorage->description);

    });
