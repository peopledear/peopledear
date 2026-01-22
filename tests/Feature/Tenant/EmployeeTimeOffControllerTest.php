<?php

declare(strict_types=1);

use App\Enums\PeopleDear\RequestStatus;
use App\Models\Employee;
use App\Models\Period;
use App\Models\TimeOffRequest;
use App\Models\TimeOffType;
use App\Models\VacationBalance;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

beforeEach(function (): void {

    $this->period = Period::factory()
        ->for($this->organization)
        ->active()
        ->create();

    $this->timeOffType = TimeOffType::factory()
        ->for($this->organization)
        ->create();

    $this->vacationType = TimeOffType::factory()
        ->for($this->organization)
        ->create(['name' => 'Vacation']);

    $this->sickLeaveType = TimeOffType::factory()
        ->for($this->organization)
        ->create(['name' => 'Sick Leave']);

    $this->userEmployee = Employee::factory()
        ->for($this->organization)
        ->for($this->employee)
        ->create();

});

test('can store a time off request',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        VacationBalance::factory()->create([
            'employee_id' => $this->userEmployee->id,
            'year' => 2025,
            'from_last_year' => 0,
            'accrued' => 2000,
            'taken' => 0,
        ]);

        $response = $this->actingAs($this->employee)
            ->post(tenant_route('tenant.employee.time-offs.store', $this->tenant), [
                'employee_id' => $this->userEmployee->id,
                'organization_id' => $this->organization->id,
                'period_id' => $this->period->id,
                'time_off_type_id' => $this->vacationType->id,
                'start_date' => '2025-01-15T00:00:00.000Z',
                'end_date' => '2025-01-17T00:00:00.000Z',
                'is_half_day' => false,
            ]);

        $response->assertRedirect(tenant_route('tenant.employee.overview', $this->tenant))
            ->assertSessionHas('status', 'Time off request submitted successfully.');

        $this->assertDatabaseHas('time_off_requests', [
            'employee_id' => $this->userEmployee->id,
            'organization_id' => $this->organization->id,
            'time_off_type_id' => $this->vacationType->id,
        ]);
    });

test('validates required fields when storing time off request',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->actingAs($this->employee)
            ->post(tenant_route('tenant.employee.time-offs.store', $this->tenant), []);

        $response->assertSessionHasErrors([
            'employee_id',
            'organization_id',
            'time_off_type_id',
            'start_date',
        ]);
    });

test('validates type is required',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        VacationBalance::factory()->create([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->userEmployee->id,
            'year' => 2025,
            'from_last_year' => 0,
            'accrued' => 100,
            'taken' => 0,
        ]);

        $response = $this->actingAs($this->employee)
            ->post(tenant_route('tenant.employee.time-offs.store', $this->tenant), [
                'employee_id' => $this->userEmployee->id,
                'organization_id' => $this->organization->id,
                'period_id' => $this->period->id,
                'start_date' => '2025-01-15T00:00:00.000Z',
                'end_date' => '2025-01-20T00:00:00.000Z',
                'is_half_day' => false,
            ]);

        $response
            ->assertSessionHasErrors([
                'time_off_type_id' => 'The time off type id field is required.',
            ]);
    });

test('validates end date must be after or equal to start date',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->actingAs($this->employee)
            ->post(tenant_route('tenant.employee.time-offs.store', $this->tenant), [
                'employee_id' => $this->userEmployee->id,
                'organization_id' => $this->organization->id,
                'time_off_type_id' => $this->vacationType->id,
                'start_date' => '2025-01-17',
                'end_date' => '2025-01-15',
                'is_half_day' => false,
            ]);

        $response->assertSessionHasErrors(['end_date']);
    });

test('unauthenticated user cannot store time off request',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->post(tenant_route('tenant.employee.time-offs.store', $this->tenant), []);

        $response->assertRedirect(tenant_route('tenant.auth.login', $this->tenant));
    });

test('authenticated employee can access time offs page',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->actingAs($this->employee)
            ->get(tenant_route('tenant.employee.time-offs.index', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('employee-time-offs/index')
                ->has('timeOffRequests')
                ->has('statuses')
                ->has('filters')
            );
    });

test('unauthenticated user is redirected to login on index page',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->get(tenant_route('tenant.employee.time-offs.index', $this->tenant));

        $response->assertRedirect(tenant_route('tenant.auth.login', $this->tenant));
    });

test('employee sees paginated time off requests with 20 per page',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        TimeOffRequest::factory()
            ->for($this->userEmployee)
            ->for($this->organization)
            ->for($this->period)
            ->for($this->timeOffType, 'type')
            ->count(25)
            ->create();

        $response = $this->actingAs($this->employee)
            ->get(tenant_route('tenant.employee.time-offs.index', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('employee-time-offs/index')
                ->has('timeOffRequests.data', 20)
                ->where('timeOffRequests.per_page', 20)
                ->where('timeOffRequests.total', 25)
            );
    });

test('page displays empty state when user has no requests',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->actingAs($this->employee)
            ->get(tenant_route('tenant.employee.time-offs.index', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('employee-time-offs/index')
                ->has('timeOffRequests.data', 0)
            );
    });

test('requests are ordered by created_at desc',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var TimeOffRequest $oldRequest */
        $oldRequest = TimeOffRequest::factory()
            ->for($this->userEmployee)
            ->for($this->organization)
            ->for($this->period)
            ->for($this->timeOffType, 'type')
            ->create(['created_at' => now()->subDays(2)]);

        /** @var TimeOffRequest $newRequest */
        $newRequest = TimeOffRequest::factory()
            ->for($this->userEmployee)
            ->for($this->organization)
            ->for($this->period)
            ->for($this->timeOffType, 'type')
            ->create(['created_at' => now()]);

        $response = $this->actingAs($this->employee)
            ->get(tenant_route('tenant.employee.time-offs.index', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('employee-time-offs/index')
                ->where('timeOffRequests.data.0.id', $newRequest->id)
                ->where('timeOffRequests.data.1.id', $oldRequest->id)
            );
    });

test('filtering by status returns only matching requests',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        TimeOffRequest::factory()
            ->for($this->userEmployee)
            ->for($this->organization)
            ->for($this->period)
            ->for($this->timeOffType, 'type')
            ->create(['status' => RequestStatus::Pending]);

        TimeOffRequest::factory()
            ->for($this->userEmployee)
            ->for($this->organization)
            ->for($this->period)
            ->for($this->timeOffType, 'type')
            ->create(['status' => RequestStatus::Approved]);

        $response = $this->actingAs($this->employee)
            ->get(tenant_route('tenant.employee.time-offs.index', $this->tenant, [
                'status' => RequestStatus::Pending->value,
            ]));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('employee-time-offs/index')
                ->has('timeOffRequests.data', 1)
                ->where('timeOffRequests.data.0.status.value', RequestStatus::Pending->value)
            );
    });

test('clearing status filter returns all requests',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        TimeOffRequest::factory()
            ->for($this->userEmployee)
            ->for($this->organization)
            ->for($this->period)
            ->for($this->timeOffType, 'type')
            ->create(['status' => RequestStatus::Pending]);

        TimeOffRequest::factory()
            ->for($this->userEmployee)
            ->for($this->organization)
            ->for($this->period)
            ->for($this->timeOffType, 'type')
            ->create(['status' => RequestStatus::Approved]);

        $response = $this->actingAs($this->employee)
            ->get(tenant_route('tenant.employee.time-offs.index', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('employee-time-offs/index')
                ->has('timeOffRequests.data', 2)
            );
    });

test('status filter persists in URL query params',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->actingAs($this->employee)
            ->get(tenant_route('tenant.employee.time-offs.index', $this->tenant, [
                'status' => RequestStatus::Pending->value,
            ]));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('employee-time-offs/index')
                ->where('filters.status', RequestStatus::Pending->value)
            );
    });

test('filtering by type returns only matching requests',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        TimeOffRequest::factory()
            ->for($this->userEmployee)
            ->for($this->organization)
            ->for($this->period)
            ->for($this->vacationType, 'type')
            ->create();

        TimeOffRequest::factory()
            ->for($this->userEmployee)
            ->for($this->organization)
            ->for($this->period)
            ->for($this->sickLeaveType, 'type')
            ->create();

        $response = $this->actingAs($this->employee)
            ->get(tenant_route('tenant.employee.time-offs.index', $this->tenant, [
                'type' => $this->vacationType->id,
            ]));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('employee-time-offs/index')
                ->has('timeOffRequests.data', 1)
                ->where('timeOffRequests.data.0.type.name', 'Vacation')
            );
    });

test('combined status and type filters return correct results',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        TimeOffRequest::factory()
            ->for($this->userEmployee)
            ->for($this->organization)
            ->for($this->period)
            ->for($this->vacationType, 'type')
            ->create(['status' => RequestStatus::Pending]);

        TimeOffRequest::factory()
            ->for($this->userEmployee)
            ->for($this->organization)
            ->for($this->period)
            ->for($this->vacationType, 'type')
            ->create(['status' => RequestStatus::Approved]);

        TimeOffRequest::factory()
            ->for($this->userEmployee)
            ->for($this->organization)
            ->for($this->period)
            ->for($this->sickLeaveType, 'type')
            ->create(['status' => RequestStatus::Pending]);

        $response = $this->actingAs($this->employee)
            ->get(tenant_route('tenant.employee.time-offs.index', $this->tenant, [
                'status' => RequestStatus::Pending->value,
                'type' => $this->vacationType->id,
            ]));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('employee-time-offs/index')
                ->has('timeOffRequests.data', 1)
                ->where('timeOffRequests.data.0.status.value', RequestStatus::Pending->value)
                ->where('timeOffRequests.data.0.type.name', 'Vacation')
            );
    });

test('type filter persists in URL query params',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->actingAs($this->employee)
            ->get(tenant_route('tenant.employee.time-offs.index', $this->tenant, [
                'type' => $this->vacationType->id,
            ]));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('employee-time-offs/index')
                ->where('filters.type', $this->vacationType->id)
            );
    });

test('renders the create time off request page',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->actingAs($this->employee)
            ->get(tenant_route('tenant.employee.time-offs.create', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('employee-time-offs/create')
                ->has('employee')
                ->has('period')
                ->has('timeOffTypes')
            );
    });

test('unauthenticated user is redirected to login',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->get(tenant_route('tenant.employee.time-offs.create', $this->tenant));

        $response->assertRedirect(tenant_route('tenant.auth.login', $this->tenant));
    });
