<?php

declare(strict_types=1);

use App\Actions\Permission\CreatePermission;

beforeEach(function (): void {

    $this->action = resolve(CreatePermission::class);

});

test('returns existing permission if already exists',
    /**
     * @throws Throwable
     */
    function (): void {

        $firstPermission = $this->action->handle(
            permission: 'time-off-type:manage',
        );

        $secondPermission = $this->action->handle(
            permission: 'time-off-type:manage',
        );

        expect($firstPermission->id)
            ->toBe($secondPermission->id);

    });

test('creates a permission',
    /**
     * @throws Throwable
     */
    function (): void {

        $permission = $this->action->handle(
            permission: 'time-off-type:manage',
        );

        expect($permission->name)
            ->toBe('time-off-type:manage');

    });
