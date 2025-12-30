<?php

declare(strict_types=1);

use App\Actions\Permission\CreatePermission;

beforeEach(function (): void {

    $this->action = resolve(CreatePermission::class);

});

test('creates a permission',
    /**
     * @throws Throwable
     */
    function (): void {

        $permission = $this->action->handle(
            permissionName: 'time-off-type:manage',
        );

        expect($permission->name)
            ->toBe('time-off-type:manage');

    });
