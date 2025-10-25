<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\UpdateOrganizationAction;
use App\Data\UpdateOrganizationData;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class OrganizationController
{
    public function edit(): Response
    {
        /** @var Organization $organization */
        $organization = Organization::query()
            ->with('offices.address')
            ->firstOrFail();

        return Inertia::render('admin/settings/organization', [
            'organization' => $organization,
        ]);
    }

    public function update(
        UpdateOrganizationRequest $request,
        UpdateOrganizationAction $action
    ): RedirectResponse {
        /** @var Organization $organization */
        $organization = Organization::query()->firstOrFail();

        $data = UpdateOrganizationData::from($request->validated());

        $action->handle($organization, $data);

        return to_route('org.settings.organization.edit')
            ->with('success', 'Organization updated successfully');
    }
}
