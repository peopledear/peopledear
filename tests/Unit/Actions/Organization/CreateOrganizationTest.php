<?php

declare(strict_types=1);

use App\Actions\Organization\CreateOrganization;
use App\Data\PeopleDear\Organization\CreateOrganizationData;
use App\Models\Country;
use App\Models\Organization;

beforeEach(function (): void {
    $this->action = app(CreateOrganization::class);
    $this->country = Country::factory()
        ->createQuietly();
});

test('creates organization with provided data',
    /**
     * @throws Exception
     */
    function (): void {
        $data = new CreateOrganizationData(
            name: 'Test Organization',
            countryId: $this->country->id,
        );

        $organization = $this->action->handle($data);

        expect($organization)
            ->toBeInstanceOf(Organization::class)
            ->id->toBeInt()
            ->name->toBe('Test Organization')
            ->country_id->toBe($this->country->id);

        /** @var Organization $persistedOrganization */
        $persistedOrganization = Organization::query()
            ->where('id', $organization->id)
            ->first();

        expect($persistedOrganization)
            ->not->toBeNull()
            ->name->toBe('Test Organization')
            ->country_id->toBe($this->country->id);
    });

test('creates organization with minimal data',
    /**
     * @throws Exception
     */
    function (): void {

        $data = new CreateOrganizationData(
            name: 'Minimal Organization',
            countryId: $this->country->id,
        );

        $organization = $this->action->handle($data);

        expect($organization)
            ->toBeInstanceOf(Organization::class)
            ->name->toBe('Minimal Organization')
            ->country_id->toBe($this->country->id)
            ->vat_number->toBeNull()
            ->ssn->toBeNull()
            ->phone->toBeNull();
    });

test('returns refreshed organization instance',
    /**
     * @throws Exception
     */
    function (): void {

        $data = new CreateOrganizationData(
            name: 'Test Organization',
            countryId: $this->country->id,
        );

        $organization = $this->action->handle($data);

        // Verify the organization is properly persisted and refreshed
        expect($organization->exists)->toBeTrue()
            ->and($organization->id)->toBeInt()
            ->and($organization->name)->toBe('Test Organization')
            ->and($organization->country_id)->toBe($this->country->id);
    });
