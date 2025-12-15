<?php

declare(strict_types=1);

use App\Actions\Employee\UpdateEmployee;
use App\Data\PeopleDear\Employee\UpdateEmployeeData;
use App\Enums\PeopleDear\EmploymentStatus;
use App\Models\Employee;
use App\Models\Office;
use App\Models\Organization;
use App\Models\User;
use Carbon\CarbonImmutable;

beforeEach(function (): void {
    $this->action = resolve(UpdateEmployee::class);
});

test('updates employee with all fields',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Office $oldOffice */
        $oldOffice = Office::factory()->create([
            'organization_id' => $organization->id,
        ]);

        /** @var Office $newOffice */
        $newOffice = Office::factory()->create([
            'organization_id' => $organization->id,
        ]);

        /** @var User $oldUser */
        $oldUser = User::factory()->create();

        /** @var User $newUser */
        $newUser = User::factory()->create();

        /** @var Employee $employee */
        $employee = Employee::factory()->create([
            'organization_id' => $organization->id,
            'office_id' => $oldOffice->id,
            'user_id' => $oldUser->id,
            'name' => 'Old Name',
            'employee_number' => 'EMP-OLD',
            'employment_status' => EmploymentStatus::Active,
            'email' => 'old@example.com',
            'phone' => '+1-555-0000',
            'job_title' => 'Old Title',
        ]);

        $data = new UpdateEmployeeData(
            name: 'New Name',
            employee_number: 'EMP-NEW',
            employment_status: EmploymentStatus::Inactive,
            email: 'new@example.com',
            phone: '+1-555-9999',
            job_title: 'New Title',
            hire_date: CarbonImmutable::now(),
            office_id: $newOffice->id,
            user_id: $newUser->id,
        );

        $result = $this->action->handle($employee, $data);

        expect($result->name)->toBe('New Name')
            ->and($result->employee_number)->toBe('EMP-NEW')
            ->and($result->employment_status)->toBe(EmploymentStatus::Inactive)
            ->and($result->email)->toBe('new@example.com')
            ->and($result->phone)->toBe('+1-555-9999')
            ->and($result->job_title)->toBe('New Title')
            ->and($result->office_id)->toBe($newOffice->id)
            ->and($result->user_id)->toBe($newUser->id);
    });

test('updates employee with partial fields',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Employee $employee */
        $employee = Employee::factory()->create([
            'organization_id' => $organization->id,
            'name' => 'Original Name',
            'employee_number' => 'EMP-ORIG',
            'employment_status' => EmploymentStatus::Active,
            'email' => 'original@example.com',
        ]);

        $data = UpdateEmployeeData::from([
            'name' => 'Updated Name',
        ]);

        $result = $this->action->handle($employee, $data);

        expect($result->name)->toBe('Updated Name')
            ->and($result->employee_number)->toBe('EMP-ORIG')
            ->and($result->email)->toBe('original@example.com');
    });

test('updates employee clearing nullable fields',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Employee $employee */
        $employee = Employee::factory()->create([
            'organization_id' => $organization->id,
            'email' => 'test@example.com',
            'phone' => '+1-555-1234',
            'job_title' => 'Engineer',
        ]);

        $data = UpdateEmployeeData::from([
            'email' => null,
            'phone' => null,
            'job_title' => null,
        ]);

        $result = $this->action->handle($employee, $data);

        expect($result->email)->toBeNull()
            ->and($result->phone)->toBeNull()
            ->and($result->job_title)->toBeNull();
    });

test('updates employee employment status',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Employee $employee */
        $employee = Employee::factory()->create([
            'organization_id' => $organization->id,
            'employment_status' => EmploymentStatus::Active,
        ]);

        $data = UpdateEmployeeData::from([
            'employment_status' => EmploymentStatus::Terminated,
        ]);

        $result = $this->action->handle($employee, $data);

        expect($result->employment_status)->toBe(EmploymentStatus::Terminated);
    });
