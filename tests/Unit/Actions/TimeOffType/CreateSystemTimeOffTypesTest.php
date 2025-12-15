<?php

declare(strict_types=1);

use App\Actions\TymeOffType\CreateSystemTimeOffTypes;
use App\Enums\PeopleDear\SystemRole;
use App\Models\Organization;
use App\Models\TimeOffType;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->organization = Organization::factory()
        ->createQuietly();

    $this->action = resolve(CreateSystemTimeOffTypes::class);
    $this->peopleManagerRole = Role::findByName(SystemRole::PeopleManager->value);
});

test('sick leave does not require approval and fallback role is null',
    /**
     * @throws Throwable
     */
    function (): void {

        $this->action->handle($this->organization);

        /** @var TimeOffType $sickLeaveTimeOffType */
        $sickLeaveTimeOffType = TimeOffType::query()
            ->where('name', 'Sick Leave')
            ->first();

        expect($sickLeaveTimeOffType)
            ->toBeInstanceOf(TimeOffType::class)
            ->and($sickLeaveTimeOffType->requires_approval)
            ->toBeFalse()
            ->and($sickLeaveTimeOffType->requires_justification)
            ->toBeTrue()
            ->and($sickLeaveTimeOffType->requires_justification_document)
            ->toBeTrue()
            ->and($sickLeaveTimeOffType->fallbackApprovalRole)
            ->toBeNull();

    });

test('vacation has a people_manager role as the approval fallback',
    /**
     * @throws Throwable
     */
    function (): void {

        $this->action->handle($this->organization);

        /** @var TimeOffType $vacationTimeOffType */
        $vacationTimeOffType = TimeOffType::query()
            ->where('name', 'Vacation')
            ->first();

        expect($vacationTimeOffType)
            ->toBeInstanceOf(TimeOffType::class)
            ->and($vacationTimeOffType->requires_approval)
            ->toBeTrue()
            ->and($vacationTimeOffType->requires_justification)
            ->toBeFalse()
            ->and($vacationTimeOffType->fallbackApprovalRole->id)
            ->toBe($this->peopleManagerRole->id);

    });

test('it creates system time off types',
    /**
     * @throws Throwable
     */
    function (): void {

        $this->action->handle($this->organization);

        /** @var Collection<int, TimeOffType> $timeOffTypes */
        $timeOffTypes = $this->organization->timeOffTypes()
            ->where('is_system', true)
            ->get();

        expect($timeOffTypes)
            ->toHaveCount(2);

    });
