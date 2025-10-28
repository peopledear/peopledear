<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Data\PeopleDear\Organization\CreateOrganizationData;
use App\Models\Organization;

final readonly class CreateOrganization
{
    /**
     * Create a new organization with the provided data.
     */
    public function handle(CreateOrganizationData $data): Organization
    {
        $organization = Organization::query()->create([
            'name' => $data->name,
        ]);

        return $organization->refresh();
    }
}
