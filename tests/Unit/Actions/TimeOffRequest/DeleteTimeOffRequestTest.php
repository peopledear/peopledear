<?php

declare(strict_types=1);

use App\Actions\TimeOffRequest\DeleteTimeOffRequest;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\TimeOffRequest;

beforeEach(function (): void {
    $this->action = app(DeleteTimeOffRequest::class);
});

test('deletes time off', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->createQuietly();

    $timeOffId = $timeOff->id;

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();
});

test('deletes time off but organization still exists', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->createQuietly([
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
    $employee = Employee::factory()->createQuietly();

    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->createQuietly([
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
    $timeOff = TimeOffRequest::factory()->pending()->createQuietly();

    $timeOffId = $timeOff->id;

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();
});

test('deletes approved time off', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->approved()->createQuietly();

    $timeOffId = $timeOff->id;

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();
});

test('deletes rejected time off', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->rejected()->createQuietly();

    $timeOffId = $timeOff->id;

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();
});

test('deletes cancelled time off', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->cancelled()->createQuietly();

    $timeOffId = $timeOff->id;

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();
});

test('deletes half day time off', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->halfDay()->createQuietly();

    $timeOffId = $timeOff->id;

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();
});

test('deletes multi day time off', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->multiDay()->createQuietly();

    $timeOffId = $timeOff->id;

    $this->action->handle($timeOff);

    /** @var TimeOffRequest|null $deletedTimeOff */
    $deletedTimeOff = TimeOffRequest::query()->find($timeOffId);

    expect($deletedTimeOff)->toBeNull();
});
