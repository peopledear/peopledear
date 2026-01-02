<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Data\PeopleDear\Organization\CreateOrganizationData;
use App\Models\Organization;

final readonly class CreateOrganization
{
    public function __construct(
        private MakeOrganizationIdentifier $makeOrganizationSlug,
        private MakeOrganizationResourceKey $makeOrganizationResourceKey,
    ) {}

    public function handle(CreateOrganizationData $data): Organization
    {

        $data->additional([
            'identifier' => $this->makeOrganizationSlug
                ->handle($data->name),
            'resource_key' => $this->makeOrganizationResourceKey
                ->handle(),
        ]);

        $organization = Organization::query()
            ->create(
                $data->toArray());

        return $organization->refresh();
    }
}
