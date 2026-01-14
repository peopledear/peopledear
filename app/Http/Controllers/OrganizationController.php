<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Organization\CreateOrganization;
use App\Actions\Organization\SetCurrentOrganization;
use App\Actions\Organization\UpdateOrganization;
use App\Data\PeopleDear\Country\CountryData;
use App\Data\PeopleDear\Organization\CreateOrganizationData;
use App\Data\PeopleDear\Organization\UpdateOrganizationData;
use App\Http\Requests\CreateOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Queries\CountryQuery;
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

    public function create(CountryQuery $countryQuery): Response
    {
        $countries = $countryQuery->builder()
            ->orderBy('iso_code')
            ->get();

        return Inertia::render('org/create', [
            'countries' => CountryData::collect($countries),
        ]);
    }

    public function store(
        CreateOrganizationRequest $request,
        CreateOrganization $action,
        SetCurrentOrganization $setCurrentOrganization,
    ): RedirectResponse {
        $data = CreateOrganizationData::from($request->validated());

        $organization = $action->handle($data);

        $setCurrentOrganization->handle($organization);

        return to_route('tenant.org.overview', [
            'tenant' => $organization->identifier,
        ])
            ->with('success', 'Organization created successfully');
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
