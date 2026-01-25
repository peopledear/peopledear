<?php

declare(strict_types=1);

use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

test('tenant route with miss configuration throws exception',
    /**
     * @throws MisconfigurationException
     */
    function (): void {

        $tenant = App\Models\Organization::factory()->make();

        tenant_route('non.existent.route', $tenant);
    })->throws(MisconfigurationException::class);
