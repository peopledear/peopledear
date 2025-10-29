<?php

declare(strict_types=1);

use App\Actions\Organization\UpdateOrganization;
use App\Data\PeopleDear\Organization\UpdateOrganizationData;
use App\Models\Organization;

beforeEach(function (): void {
    $this->action = app(UpdateOrganization::class);
});

test('updates organization with all fields',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly([
            'name' => 'Old Name',
            'phone' => 'Old Phone',
        ]);

        $data = UpdateOrganizationData::from([
            'name' => 'New Name',
            'vat_number' => 'VAT123',
            'ssn' => 'SSN123',
            'phone' => '+1234567890',
        ]);

        $result = $this->action->handle($organization, $data);

        expect($result->name)
            ->toBe('New Name')
            ->and($result->vat_number)
            ->toBe('VAT123')
            ->and($result->ssn)
            ->toBe('SSN123')
            ->and($result->phone)
            ->toBe('+1234567890');
    });

test('updates organization with partial fields only',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly([
            'name' => 'Old Name',
            'phone' => '+9999999999',
            'vat_number' => 'OLD_VAT',
        ]);

        $data = UpdateOrganizationData::from([
            'name' => 'New Name',
        ]);

        $result = $this->action->handle($organization, $data);

        expect($result->name)
            ->toBe('New Name')
            ->and($result->phone)
            ->toBe('+9999999999')
            ->and($result->vat_number)
            ->toBe('OLD_VAT');
    });

test('can set fields to null explicitly',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly([
            'name' => 'Test Company',
            'phone' => '+1234567890',
            'vat_number' => 'VAT123',
        ]);

        $data = UpdateOrganizationData::from([
            'phone' => null,
            'vat_number' => null,
        ]);

        $result = $this->action->handle($organization, $data);

        expect($result->name)
            ->toBe('Test Company')
            ->and($result->phone)
            ->toBeNull()
            ->and($result->vat_number)
            ->toBeNull();
    });

test('empty data object does not change anything',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly([
            'name' => 'Test Company',
            'phone' => '+1234567890',
            'vat_number' => 'VAT123',
        ]);

        $data = UpdateOrganizationData::from([]);

        $result = $this->action->handle($organization, $data);

        expect($result->name)
            ->toBe('Test Company')
            ->and($result->phone)
            ->toBe('+1234567890')
            ->and($result->vat_number)
            ->toBe('VAT123');
    });
