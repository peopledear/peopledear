<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Organization\CreateOrganization;
use App\Actions\Organization\SetCurrentOrganization;
use App\Actions\Organization\UpdateOrganization;
use App\Attributes\CurrentOrganization;
use App\Data\PeopleDear\Country\CountryData;
use App\Data\PeopleDear\Organization\CreateOrganizationData;
use App\Data\PeopleDear\Organization\UpdateOrganizationData;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Queries\CountryQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

final class OrganizationController
{
    public function index(
        Request $request,
        #[CurrentOrganization] Organization $organization
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
        StoreOrganizationRequest $request,
        CreateOrganization $action,
        SetCurrentOrganization $setCurrentOrganization,
    ): RedirectResponse {
        $data = CreateOrganizationData::from($request->validated());

        $organization = $action->handle($data);

        $setCurrentOrganization->handle($organization);

        return to_route('org.overview')
            ->with('success', 'Organization created successfully');
    }

    public function edit(
        #[CurrentOrganization] Organization $organization
    ): Response {

        Gate::authorize('view', $organization);

        return Inertia::render('org-settings-general/edit', [
            'organization' => $organization->load('offices.address'),
        ]);
    }

    public function update(
        #[CurrentOrganization] Organization $organization,
        UpdateOrganizationRequest $request,
        UpdateOrganization $action
    ): RedirectResponse {

        $data = UpdateOrganizationData::from($request->validated());

        $organization = $action->handle($organization, $data);

        return to_route('org.settings.organization.edit', [
            'organization' => $organization->id,
        ])
            ->with('success', 'Organization updated successfully');
    }
}
