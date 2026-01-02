<?php

declare(strict_types=1);

use App\Actions\Organization\MakeOrganizationResourceKey;

test('make organization resource key returns valid ULID',
    /**
     * @throws Exception
     */
    function (): void {

        $action = resolve(MakeOrganizationResourceKey::class);

        $resourceKey = $action->handle();

        expect($resourceKey)
            ->toBeString()
            ->and(Illuminate\Support\Str::isUlid($resourceKey))->toBeTrue();

    });
