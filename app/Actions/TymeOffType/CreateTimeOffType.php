<?php

declare(strict_types=1);

namespace App\Actions\TymeOffType;

use App\Data\PeopleDear\TimeOffType\CreateTimeOffTypeData;
use App\Enums\TimeOffTypeStatus;
use App\Models\Organization;
use App\Models\TimeOffType;

final readonly class CreateTimeOffType
{
    public function handle(
        Organization $organization,
        CreateTimeOffTypeData $createTimeOffTypeData
    ): TimeOffType {

        $createTimeOffTypeData->additional([
            'status' => $createTimeOffTypeData->isSystem
                ? TimeOffTypeStatus::Active
                : TimeOffTypeStatus::Pending,
            'organization_id' => $organization->id,
        ]);

        return TimeOffType::query()->create(
            $createTimeOffTypeData->toArray()
        );
    }
}
