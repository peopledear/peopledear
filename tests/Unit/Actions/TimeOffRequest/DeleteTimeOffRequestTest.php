<?php

declare(strict_types=1);

use App\Actions\TimeOffRequest\DeleteTimeOffRequest;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\TimeOffRequest;

beforeEach(function (): void {
    $this->action = resolve(DeleteTimeOffRequest::class);
});

test('deletes time off', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->create();

    $timeOffId = $timeOff->id;

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();
});

test('deletes time off but organization still exists', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->create();

    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $timeOffId = $timeOff->id;

    expect($timeOff->organization_id)->toBe($organization->id);

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();

    /** @var Organization|null $organizationStillExists */
    $organizationStillExists = Organization::query()->find($organization->id);

    expect($organizationStillExists)->not->toBeNull();
});

test('deletes time off but employee still exists', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()->create();

    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->create([
        'employee_id' => $employee->id,
    ]);

    $timeOffId = $timeOff->id;

    expect($timeOff->employee_id)->toBe($employee->id);

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();

    /** @var Employee|null $employeeStillExists */
    $employeeStillExists = Employee::query()->find($employee->id);

    expect($employeeStillExists)->not->toBeNull();
});

test('deletes pending time off', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->pending()->create();

    $timeOffId = $timeOff->id;

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();
});

test('deletes approved time off', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->approved()->create();

    $timeOffId = $timeOff->id;

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();
});

test('deletes rejected time off', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->rejected()->create();

    $timeOffId = $timeOff->id;

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();
});

test('deletes cancelled time off', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->cancelled()->create();

    $timeOffId = $timeOff->id;

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();
});

test('deletes half day time off', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->halfDay()->create();

    $timeOffId = $timeOff->id;

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();
});

test('deletes multi day time off', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->multiDay()->create();

    $timeOffId = $timeOff->id;

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();
});
