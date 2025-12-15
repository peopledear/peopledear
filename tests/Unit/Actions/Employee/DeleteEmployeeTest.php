<?php

declare(strict_types=1);

use App\Actions\Employee\DeleteEmployee;
use App\Models\Employee;
use App\Models\Organization;

beforeEach(function (): void {
    $this->action = resolve(DeleteEmployee::class);
});

test('deletes employee',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Employee $employee */
        $employee = Employee::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $employeeId = $employee->id;

        $this->action->handle($employee);

        /** @var Employee|null $deletedEmployee */
        $deletedEmployee = Employee::query()->find($employeeId);

        expect($deletedEmployee)->toBeNull();
    });

test('deletes employee with relationships',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Employee $employee */
        $employee = Employee::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $employeeId = $employee->id;

        // Verify employee exists with organization
        expect($employee->organization_id)->toBe($organization->id);

        $this->action->handle($employee);

        /** @var Employee|null $deletedEmployee */
        $deletedEmployee = Employee::query()->find($employeeId);

        expect($deletedEmployee)->toBeNull();

        // Verify organization still exists
        /** @var Organization|null $organizationStillExists */
        $organizationStillExists = Organization::query()->find($organization->id);

        expect($organizationStillExists)->not->toBeNull();
    });
