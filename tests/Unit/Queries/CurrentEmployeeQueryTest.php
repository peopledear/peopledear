<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\User;
use App\Queries\CurrentEmployeeQuery;
use Illuminate\Support\Facades\Auth;

test('gets the current user',
    /**
     * @throws Throwable
     */
    function (): void {

        /** @var User $user */
        $user = User::factory()
            ->create()
            ->fresh();

        Employee::factory()
            ->for($user)
            ->create();

        Auth::login($user);

        $query = app()->make(CurrentEmployeeQuery::class);

        $employee = $query->builder()->first();

        expect($user->id)
            ->toBe($employee->user->id);

    });
