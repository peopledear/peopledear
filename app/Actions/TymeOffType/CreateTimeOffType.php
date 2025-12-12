<?php

declare(strict_types=1);

namespace App\Actions\TymeOffType;

use App\Data\PeopleDear\TimeOffType\CreateTimeOffTypeData;
use App\Models\Organization;
use App\Models\TimeOffType;

final class CreateTimeOffType
{
    public function handle(
        Organization          $organization,
        CreateTimeOffTypeData $createTimeOffTypeData
    ): TimeOffType
    {
        return TimeOffType::query()
            ->create([
                ...$createTimeOffTypeData->toArray(),
                'organization_id' => $organization->id
            ]);
    }
}
