<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\TymeOffType\CreateTimeOffType;
use App\Attributes\CurrentOrganization;
use App\Data\PeopleDear\TimeOffType\CreateTimeOffTypeData;
use App\Data\PeopleDear\TimeOffType\TimeOffTypeData;
use App\Data\PeopleDear\TimeOffType\TimeOffUnitData;
use App\Enums\BalanceType;
use App\Enums\Icon;
use App\Enums\PeopleDear\TimeOffUnit;
use App\Http\Requests\StoreTimeOffTypeRequest;
use App\Models\Organization;
use App\Models\TimeOffType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

use function back;
use function collect;

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

    public function create(): Response
    {

        $timeOffUnits = collect(TimeOffUnit::cases())
            ->map(fn (TimeOffUnit $unit): array => [
                'value' => $unit->value,
                'label' => $unit->label(),
            ]);

        return Inertia::render('org-time-off-types/create', [
            'balanceTypes' => BalanceType::options()->toArray(),
            'timeOffUnits' => TimeOffUnitData::collect($timeOffUnits),
            'icons' => Icon::options()->toArray(),
        ]);
    }

    public function store(
        StoreTimeOffTypeRequest $request,
        CreateTimeOffType $action,
        #[CurrentOrganization] Organization $organization
    ): RedirectResponse {

        $action->handle(
            $organization,
            CreateTimeOffTypeData::from($request->validated())
        );

        return back();

    }
}
