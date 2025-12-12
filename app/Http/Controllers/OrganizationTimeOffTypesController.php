<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Attributes\CurrentOrganization;
use App\Data\PeopleDear\TimeOffType\TimeOffTypeData;
use App\Models\Organization;
use App\Models\TimeOffType;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

final class OrganizationTimeOffTypesController
{
    public function index(#[CurrentOrganization] Organization $organization): Response
    {

        $timeOffTypes = TimeOffType::query()
            ->where('organization_id', $organization->id)
            ->get();

        return Inertia::render('org-time-off-types/index', [
            'timeOffTypes' => TimeOffTypeData::collect($timeOffTypes, Collection::class),
        ]);
    }
}
