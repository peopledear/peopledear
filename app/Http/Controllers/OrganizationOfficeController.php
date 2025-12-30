<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Office\CreateOffice;
use App\Actions\Office\DeleteOffice;
use App\Actions\Office\UpdateOffice;
use App\Data\PeopleDear\Office\CreateOfficeData;
use App\Data\PeopleDear\Office\UpdateOfficeData;
use App\Http\Requests\StoreOfficeRequest;
use App\Http\Requests\UpdateOfficeRequest;
use App\Models\Office;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Throwable;

final class OrganizationOfficeController
{
    /**
     * @throws Throwable
     */
    public function store(
        StoreOfficeRequest $request,
        CreateOffice $action
    ): RedirectResponse {
        /** @var Organization $organization */
        $organization = Organization::query()->firstOrFail();

        $data = CreateOfficeData::from($request->validated());

        $action->handle($data, $organization);

        return to_route('org.settings.organization.edit', [
            'organization' => $organization->id,
        ])
            ->with('success', 'Office created successfully');
    }

    public function update(
        UpdateOfficeRequest $request,
        Office $office,
        UpdateOffice $action
    ): RedirectResponse {
        $data = UpdateOfficeData::from($request->validated());

        $action->handle($office, $data);

        return to_route('org.settings.organization.edit', [
            'organization' => $office->organization_id,
        ])
            ->with('success', 'Office updated successfully');
    }

    public function destroy(
        Office $office,
        DeleteOffice $action
    ): RedirectResponse {

        Gate::authorize('delete', $office);

        $action->handle($office);

        return to_route('org.settings.organization.edit', [
            'organization' => $office->organization_id,
        ])
            ->with('success', 'Office deleted successfully');
    }
}
