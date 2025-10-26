<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateOffice;
use App\Actions\DeleteOffice;
use App\Actions\UpdateOffice;
use App\Data\CreateOfficeData;
use App\Data\UpdateOfficeData;
use App\Http\Requests\CreateOfficeRequest;
use App\Http\Requests\UpdateOfficeRequest;
use App\Models\Office;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

final class OrganizationOfficeController
{
    public function store(
        CreateOfficeRequest $request,
        CreateOffice $action
    ): RedirectResponse {
        /** @var Organization $organization */
        $organization = Organization::query()->firstOrFail();

        $data = CreateOfficeData::from($request->validated());

        $action->handle($data, $organization);

        return to_route('org.settings.organization.edit')
            ->with('success', 'Office created successfully');
    }

    public function update(
        UpdateOfficeRequest $request,
        Office $office,
        UpdateOffice $action
    ): RedirectResponse {
        $data = UpdateOfficeData::from($request->validated());

        $action->handle($office, $data);

        return to_route('org.settings.organization.edit')
            ->with('success', 'Office updated successfully');
    }

    public function destroy(
        Office $office,
        DeleteOffice $action
    ): RedirectResponse {
        $action->handle($office);

        return to_route('org.settings.organization.edit')
            ->with('success', 'Office deleted successfully');
    }
}
