<?php

declare(strict_types=1);

use App\Enums\EmploymentStatus;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Organization;
use App\Models\User;
use App\Models\VacationBalance;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

test('employee has organization relationship',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly();

        expect($employee->organization())
            ->toBeInstanceOf(BelongsTo::class);
    });

test('employee has location relationship',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly();

        expect($employee->location())
            ->toBeInstanceOf(BelongsTo::class);
    });

test('employee organization relationship is properly loaded',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly();

        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly([
            'organization_id' => $organization->id,
        ]);

        $employee->load('organization');

        expect($employee->organization)
            ->toBeInstanceOf(Organization::class)
            ->and($employee->organization->id)
            ->toBe($organization->id);
    });

test('employee location relationship is properly loaded',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()->createQuietly();

        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly([
            'location_id' => $location->id,
        ]);

        $employee->load('location');

        expect($employee->location)
            ->toBeInstanceOf(Location::class)
            ->and($employee->location->id)
            ->toBe($location->id);
    });

test('employee location can be null',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly([
            'location_id' => null,
        ]);

        $employee->load('location');

        expect($employee->location)->toBeNull();
    });

test('employee has user relationship',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly();

        expect($employee->user())->toBeInstanceOf(BelongsTo::class);
    });

test('employee user relationship is properly loaded',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()->createQuietly();

        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly([
            'user_id' => $user->id,
        ]);

        $employee->load('user');

        expect($employee->user)
            ->toBeInstanceOf(User::class)
            ->and($employee->user->id)
            ->toBe($user->id);
    });

test('employee user can be null',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly([
            'user_id' => null,
        ]);

        $employee->load('user');

        expect($employee->user)->toBeNull();
    });

test('employee employment_status is cast to EmploymentStatus enum',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly([
            'employment_status' => EmploymentStatus::Active,
        ]);

        expect($employee->employment_status)
            ->toBeInstanceOf(EmploymentStatus::class)
            ->and($employee->employment_status)
            ->toBe(EmploymentStatus::Active)
            ->and($employee->employment_status->value)
            ->toBe(1)
            ->and($employee->employment_status->label())
            ->toBe('Active');
    });

test('employee hire_date is cast to date',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly([
            'hire_date' => '2024-01-15',
        ]);

        expect($employee->hire_date)
            ->toBeInstanceOf(Carbon\CarbonInterface::class)
            ->and($employee->hire_date->format('Y-m-d'))
            ->toBe('2024-01-15');
    });

test('employee number is unique',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee1 */
        $employee1 = Employee::factory()->createQuietly([
            'employee_number' => 'EMP-1234',
        ]);

        expect(fn () => Employee::factory()->createQuietly([
            'employee_number' => 'EMP-1234',
        ]))
            ->toThrow(Illuminate\Database\QueryException::class);
    });

test('employee email can be null',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly([
            'email' => null,
        ]);

        expect($employee->email)->toBeNull();
    });

test('employee job_title can be null',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly([
            'job_title' => null,
        ]);

        expect($employee->job_title)->toBeNull();
    });

test('employee hire_date can be null',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly([
            'hire_date' => null,
        ]);

        expect($employee->hire_date)->toBeNull();
    });

test('employee has vacation balances relationship',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly();

        expect($employee->vacationBalances())->toBeInstanceOf(HasMany::class);
    });

test('employee has manager relationship',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly();

        expect($employee->manager())->toBeInstanceOf(BelongsTo::class);
    });

test('employee manager relationship is properly loaded',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $manager */
        $manager = Employee::factory()->createQuietly();

        /** @var Employee $employee */
        $employee = Employee::factory()
            ->for($manager->organization)
            ->withManager($manager)
            ->createQuietly();

        $employee->load('manager');

        expect($employee->manager)
            ->toBeInstanceOf(Employee::class)
            ->and($employee->manager->id)
            ->toBe($manager->id);
    });

test('employee manager can be null',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly([
            'manager_id' => null,
        ]);

        $employee->load('manager');

        expect($employee->manager)->toBeNull();
    });

test('employee has direct reports relationship',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly();

        expect($employee->directReports())->toBeInstanceOf(HasMany::class);
    });

test('employee direct reports relationship is properly loaded',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $manager */
        $manager = Employee::factory()->createQuietly();

        /** @var Employee $report1 */
        $report1 = Employee::factory()
            ->for($manager->organization)
            ->withManager($manager)
            ->createQuietly();

        /** @var Employee $report2 */
        $report2 = Employee::factory()
            ->for($manager->organization)
            ->withManager($manager)
            ->createQuietly();

        $manager->load('directReports');

        expect($manager->directReports)
            ->toHaveCount(2)
            ->and($manager->directReports->pluck('id')->toArray())
            ->toContain($report1->id, $report2->id);
    });

test('employee vacation balances relationship is properly loaded',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly();

        /** @var VacationBalance $vacationBalance */
        $vacationBalance = VacationBalance::factory()
            ->for($employee)
            ->for($employee->organization)
            ->createQuietly();

        $employee->load('vacationBalances');

        expect($employee->vacationBalances)
            ->toHaveCount(1)
            ->and($employee->vacationBalances->first()->id)
            ->toBe($vacationBalance->id);
    });

test('to array',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Employee $employee */
        $employee = Employee::factory()
            ->createQuietly()
            ->refresh();

        expect(array_keys($employee->toArray()))
            ->toBe([
                'id',
                'created_at',
                'updated_at',
                'organization_id',
                'location_id',
                'user_id',
                'name',
                'email',
                'phone',
                'employee_number',
                'job_title',
                'hire_date',
                'employment_status',
                'manager_id',
            ]);
    });
