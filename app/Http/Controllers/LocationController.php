<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Location\CreateLocation;
use App\Actions\Location\DeleteLocation;
use App\Actions\Location\UpdateLocation;
use App\Data\PeopleDear\Location\CreateLocationData;
use App\Data\PeopleDear\Location\UpdateLocationData;
use App\Http\Requests\CreateLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Sprout\Attributes\CurrentTenant;
use Throwable;

final class LocationController
{
    /**
     * @throws Throwable
     */
    public function store(
        #[CurrentTenant] Organization $organization,
        CreateLocationRequest $request,
        CreateLocation $action
    ): RedirectResponse {

        $action->handle(
            organization: $organization,
            data: CreateLocationData::from($request->validated())
        );

        return to_route('tenant.settings.organization.edit', [
            'tenant' => $organization->identifier,
        ])
            ->with('success', 'Location created successfully');
    }

    public function update(
        UpdateLocationRequest $request,
        Location $location,
        UpdateLocation $action
    ): RedirectResponse {

        $action->handle(
            location: $location,
            data: UpdateLocationData::from($request->validated())
        );

        return to_route('tenant.settings.organization.edit', [
            'tenant' => $location->organization->identifier,
        ])
            ->with('success', 'Location updated successfully');
    }

    /**
     * @throws Throwable
     */
    public function destroy(
        Location $location,
        DeleteLocation $action
    ): RedirectResponse {
        Gate::authorize('delete', $location);

        $action->handle($location);

        return to_route('tenant.settings.organization.edit', [
            'tenant' => $location->organization->identifier,
        ])
            ->with('success', 'Location deleted successfully');
    }
}
