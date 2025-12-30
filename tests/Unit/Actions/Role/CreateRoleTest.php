<?php

declare(strict_types=1);

use App\Actions\Role\CreateRole;
use App\Enums\UserRole;

beforeEach(function (): void {
    $this->action = resolve(CreateRole::class);
});

test('handles creation of existing role',
    /**
     * @throws Throwable
     */
    function (): void {

        $role = $this->action->handle(UserRole::Manager);

        expect($role->name)
            ->toBe(UserRole::Manager->value);

    });

test('creates a role',
    /**
     * @throws Throwable
     */
    function (): void {

        $role = $this->action->handle('editor');

        expect($role->name)
            ->toBe('editor');

    });
