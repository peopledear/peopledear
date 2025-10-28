<?php

declare(strict_types=1);

use App\Actions\CreateOrganization;
use App\Data\PeopleDear\Organization\CreateOrganizationData;
use App\Models\Organization;

beforeEach(function (): void {
    $this->action = new CreateOrganization;
});

test('creates organization with provided data', function (): void {
    $data = new CreateOrganizationData(
        name: 'Test Organization',
    );

    $organization = $this->action->handle($data);

    expect($organization)
        ->toBeInstanceOf(Organization::class)
        ->id->toBeInt()
        ->name->toBe('Test Organization');

    /** @var Organization $persistedOrganization */
    $persistedOrganization = Organization::query()
        ->where('id', $organization->id)
        ->first();

    expect($persistedOrganization)
        ->not->toBeNull()
        ->name->toBe('Test Organization');
});

test('creates organization with minimal data', function (): void {
    $data = new CreateOrganizationData(
        name: 'Minimal Organization',
    );

    $organization = $this->action->handle($data);

    expect($organization)
        ->toBeInstanceOf(Organization::class)
        ->name->toBe('Minimal Organization')
        ->vat_number->toBeNull()
        ->ssn->toBeNull()
        ->phone->toBeNull();
});

test('returns refreshed organization instance', function (): void {
    $data = new CreateOrganizationData(
        name: 'Test Organization',
    );

    $organization = $this->action->handle($data);

    // Verify the organization is properly persisted and refreshed
    expect($organization->exists)->toBeTrue()
        ->and($organization->id)->toBeInt()
        ->and($organization->name)->toBe('Test Organization');
});
