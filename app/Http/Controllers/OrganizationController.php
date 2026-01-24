<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Organization\UpdateOrganization;
use App\Data\PeopleDear\Organization\UpdateOrganizationData;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Sprout\Attributes\CurrentTenant;

final class OrganizationController
{
    public function index(
        #[CurrentTenant] Organization $organization
    ): Response {

        Gate::authorize('view', $organization);

        return Inertia::render('org/index', []);
    }

    public function edit(
        #[CurrentTenant] Organization $organization
    ): Response {

        Gate::authorize('view', $organization);

        return Inertia::render('org-settings-general/edit', [
            'organization' => $organization->load('locations.address'),
        ]);
    }

    public function update(
        UpdateOrganizationRequest $request,
        #[CurrentTenant] Organization $organization,
        UpdateOrganization $action
    ): RedirectResponse {

        Gate::authorize('update', $organization);

        $data = UpdateOrganizationData::from($request->validated());

        $organization = $action->handle($organization, $data);

        return to_route('tenant.settings.organization.edit', [
            'tenant' => $organization->identifier,
        ])
            ->with('success', 'Organization updated successfully');
    }
}
