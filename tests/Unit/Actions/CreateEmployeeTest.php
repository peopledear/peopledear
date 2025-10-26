<?php

declare(strict_types=1);

use App\Actions\CreateEmployee;
use App\Data\CreateEmployeeData;
use App\Enums\EmploymentStatus;
use App\Models\Employee;
use App\Models\Office;
use App\Models\Organization;
use App\Models\User;
use Carbon\CarbonImmutable;

beforeEach(function (): void {
    $this->action = app(CreateEmployee::class);
});

test('creates employee with all fields',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly();

        /** @var Office $office */
        $office = Office::factory()->createQuietly([
            'organization_id' => $organization->id,
        ]);

        /** @var User $user */
        $user = User::factory()->createQuietly();

        $data = new CreateEmployeeData(
            name: 'John Doe',
            employee_number: 'EMP-001',
            employment_status: EmploymentStatus::Active,
            email: 'john@example.com',
            phone: '+1-555-0100',
            job_title: 'Software Engineer',
            hire_date: CarbonImmutable::now(),
            office_id: $office->id,
            user_id: $user->id,
        );

        $result = $this->action->handle($data, $organization);

        expect($result)
            ->toBeInstanceOf(Employee::class)
            ->and($result->name)->toBe('John Doe')
            ->and($result->employee_number)->toBe('EMP-001')
            ->and($result->employment_status)->toBe(EmploymentStatus::Active)
            ->and($result->email)->toBe('john@example.com')
            ->and($result->phone)->toBe('+1-555-0100')
            ->and($result->job_title)->toBe('Software Engineer')
            ->and($result->office_id)->toBe($office->id)
            ->and($result->user_id)->toBe($user->id)
            ->and($result->organization_id)->toBe($organization->id);
    });

test('creates employee with minimal required fields',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly();

        $data = new CreateEmployeeData(
            name: 'Jane Smith',
            employee_number: 'EMP-002',
            employment_status: EmploymentStatus::Active,
            email: null,
            phone: null,
            job_title: null,
            hire_date: null,
            office_id: null,
            user_id: null,
        );

        $result = $this->action->handle($data, $organization);

        expect($result->name)->toBe('Jane Smith')
            ->and($result->employee_number)->toBe('EMP-002')
            ->and($result->employment_status)->toBe(EmploymentStatus::Active)
            ->and($result->email)->toBeNull()
            ->and($result->phone)->toBeNull()
            ->and($result->job_title)->toBeNull()
            ->and($result->hire_date)->toBeNull()
            ->and($result->office_id)->toBeNull()
            ->and($result->user_id)->toBeNull();
    });

test('creates employee with different employment status',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly();

        $data = new CreateEmployeeData(
            name: 'Bob Johnson',
            employee_number: 'EMP-003',
            employment_status: EmploymentStatus::OnLeave,
            email: null,
            phone: null,
            job_title: null,
            hire_date: null,
            office_id: null,
            user_id: null,
        );

        $result = $this->action->handle($data, $organization);

        expect($result->employment_status)->toBe(EmploymentStatus::OnLeave);
    });
