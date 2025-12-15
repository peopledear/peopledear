<?php

declare(strict_types=1);

use App\Enums\PeopleDear\RequestStatus;
use App\Models\TimeOffRequest;
use App\Queries\TimeOffRequestQuery;
use Illuminate\Support\Str;

test('selects from the correct table', function (): void {

    $query = new TimeOffRequestQuery;

    $sql = $query()
        ->make()
        ->toRawSql();

    $expectedSql = sprintf(
        'select * from "%s"',
        new TimeOffRequest()->getTable()
    );

    expect($sql)
        ->toBe($expectedSql);
});

test('time off request query builder can scope by employee and latest', function (): void {

    $query = new TimeOffRequestQuery;

    $sql = $query()
        ->ofEmployee($employeeId = Str::uuid7()->toString())
        ->latest()
        ->make()
        ->toRawSql();

    $expectedSql = sprintf(
        'select * from "%s" where "employee_id" = \'%s\' order by "created_at" desc limit 5',
        new TimeOffRequest()->getTable(),
        $employeeId
    );

    expect($sql)
        ->toBe($expectedSql);

});

test('scopes by single status using ofStatus', function (): void {

    $query = new TimeOffRequestQuery;

    $sql = $query()
        ->ofStatus(RequestStatus::Pending)
        ->make()
        ->toRawSql();

    $expectedSql = sprintf(
        'select * from "%s" where "status" = %d',
        new TimeOffRequest()->getTable(),
        RequestStatus::Pending->value
    );

    expect($sql)
        ->toBe($expectedSql);

});

test('scopes by multiple statuses using statusIn', function (): void {

    $query = new TimeOffRequestQuery;

    $sql = $query()
        ->statusIn([RequestStatus::Pending, RequestStatus::Approved])
        ->make()
        ->toRawSql();

    $expectedSql = sprintf(
        'select * from "%s" where "status" in (%d, %d)',
        new TimeOffRequest()->getTable(),
        RequestStatus::Pending->value,
        RequestStatus::Approved->value
    );

    expect($sql)
        ->toBe($expectedSql);

});

test('scopes by pending approval status', function (): void {

    $query = new TimeOffRequestQuery;

    $sql = $query()
        ->pendingApproval()
        ->make()
        ->toRawSql();

    $expectedSql = sprintf(
        'select * from "%s" where "status" = %d',
        new TimeOffRequest()->getTable(),
        RequestStatus::Pending->value
    );

    expect($sql)
        ->toBe($expectedSql);

});

test('scopes by approved status', function (): void {

    $query = new TimeOffRequestQuery;

    $sql = $query()
        ->approved()
        ->make()
        ->toRawSql();

    $expectedSql = sprintf(
        'select * from "%s" where "status" = %d',
        new TimeOffRequest()->getTable(),
        RequestStatus::Approved->value
    );

    expect($sql)
        ->toBe($expectedSql);

});

test('scopes by rejected status', function (): void {

    $query = new TimeOffRequestQuery;

    $sql = $query()
        ->rejected()
        ->make()
        ->toRawSql();

    $expectedSql = sprintf(
        'select * from "%s" where "status" = %d',
        new TimeOffRequest()->getTable(),
        RequestStatus::Rejected->value
    );

    expect($sql)
        ->toBe($expectedSql);

});

test('scopes by cancelled status', function (): void {

    $query = new TimeOffRequestQuery;

    $sql = $query()
        ->cancelled()
        ->make()
        ->toRawSql();

    $expectedSql = sprintf(
        'select * from "%s" where "status" = %d',
        new TimeOffRequest()->getTable(),
        RequestStatus::Cancelled->value
    );

    expect($sql)
        ->toBe($expectedSql);

});

test('eager loads default relations when none provided', function (): void {

    $query = new TimeOffRequestQuery;

    $builder = $query()
        ->withRelations()
        ->make();

    $eagerLoads = array_keys($builder->getEagerLoads());

    expect($eagerLoads)
        ->toContain('employee')
        ->toContain('organization')
        ->toContain('period')
        ->toContain('type');

});

test('eager loads custom relations when provided', function (): void {

    $query = new TimeOffRequestQuery;

    $builder = $query()
        ->withRelations(['employee', 'type'])
        ->make();

    $eagerLoads = array_keys($builder->getEagerLoads());

    expect($eagerLoads)
        ->toBe(['employee', 'type']);

});

test('accepts id in constructor', function (): void {

    $query = new TimeOffRequestQuery;
    $id = Str::uuid7()->toString();

    $sql = $query($id)
        ->make()
        ->toRawSql();

    $expectedSql = sprintf(
        'select * from "%s" where "id" = \'%s\'',
        new TimeOffRequest()->getTable(),
        $id
    );

    expect($sql)
        ->toBe($expectedSql);

});

test('can chain multiple scopes together', function (): void {

    $query = new TimeOffRequestQuery;
    $employeeId = Str::uuid7()->toString();

    $sql = $query()
        ->ofEmployee($employeeId)
        ->pendingApproval()
        ->latest(10)
        ->make()
        ->toRawSql();

    $expectedSql = sprintf(
        'select * from "%s" where "employee_id" = \'%s\' and "status" = %d order by "created_at" desc limit 10',
        new TimeOffRequest()->getTable(),
        $employeeId,
        RequestStatus::Pending->value
    );

    expect($sql)
        ->toBe($expectedSql);

});

test('first returns null when no results', function (): void {

    $query = new TimeOffRequestQuery;

    $result = $query()->first();

    expect($result)->toBeNull();

});

test('paginate returns paginated results', function (): void {

    $query = new TimeOffRequestQuery;

    $result = $query()->paginate(10);

    expect($result)
        ->toBeInstanceOf(Illuminate\Pagination\LengthAwarePaginator::class)
        ->and($result->perPage())
        ->toBe(10);

});

test('ofType scopes by time off type id', function (): void {

    $query = new TimeOffRequestQuery;
    $typeId = Str::uuid7()->toString();

    $sql = $query()
        ->ofType($typeId)
        ->make()
        ->toRawSql();

    $expectedSql = sprintf(
        'select * from "%s" where "time_off_type_id" = \'%s\'',
        new TimeOffRequest()->getTable(),
        $typeId
    );

    expect($sql)
        ->toBe($expectedSql);

});
