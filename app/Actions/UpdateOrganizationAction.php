<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\UpdateOrganizationData;
use App\Models\Organization;

final readonly class UpdateOrganizationAction
{
    /**
     * Update the organization with the provided data.
     */
    public function handle(Organization $organization, UpdateOrganizationData $data): Organization
    {
        $organization->update($data->toArray());

        return $organization->refresh();
    }
}
