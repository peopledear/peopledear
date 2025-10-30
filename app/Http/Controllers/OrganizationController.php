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
use Inertia\Inertia;
use Inertia\Response;

final class OrganizationController
{
    public function index(): Response
    {
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

        return to_route('org.overview')
            ->with('success', 'Organization created successfully');
    }

    public function edit(): Response
    {
        /** @var Organization $organization */
        $organization = Organization::query()
            ->with('offices.address')
            ->firstOrFail();

        return Inertia::render('org-settings-general/edit', [
            'organization' => $organization,
        ]);
    }

    public function update(
        UpdateOrganizationRequest $request,
        UpdateOrganization $action
    ): RedirectResponse {
        /** @var Organization $organization */
        $organization = Organization::query()->firstOrFail();

        $data = UpdateOrganizationData::from($request->validated());

        $action->handle($organization, $data);

        return to_route('org.settings.organization.edit')
            ->with('success', 'Organization updated successfully');
    }
}
