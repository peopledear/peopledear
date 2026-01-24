<?php

declare(strict_types=1);

namespace App;

use App\Enums\SessionKey;
use App\Models\Organization;
use Illuminate\Support\Facades\Session;
use Sprout\Facades\Sprout;

use function function_exists;

if (! function_exists('organization')) {

    function organization(): ?Organization
    {
        return Organization::query()
            ->where('id', Session::get(SessionKey::CurrentOrganization->value))
            ->first();
    }

}

if (! function_exists('tenant_route')) {

    /**
     * @param  array<string, mixed>  $parameters
     */
    function tenant_route(
        string $name,
        Organization $tenant,
        array $parameters = [],
        bool $absolute = true,
        ?string $resolver = null,
        ?string $tenancy = null,
    ): string {
        return Sprout::route(
            name: $name,
            tenant: $tenant,
            resolver: $resolver,
            tenancy: $tenancy,
            parameters: $parameters,
            absolute: $absolute,
        );
    }

}
