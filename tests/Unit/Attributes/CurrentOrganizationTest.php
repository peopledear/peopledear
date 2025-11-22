<?php

declare(strict_types=1);

use App\Attributes\CurrentOrganization;
use App\Models\Organization;
use Illuminate\Support\Facades\Session;

test('returns null when no current organization is set', function (): void {

    $attribute = new CurrentOrganization();

    Session::forget('current_organization');

    $currentOrganization = $attribute->resolve($attribute, app());

    expect($currentOrganization)
        ->toBeNull();

});

test('returns current organization id', function (): void {

    $attribute = new CurrentOrganization();
    $organization = Organization::factory()
        ->create();

    Session::put('current_organization', $organization->id);

    $currentOrganization = $attribute->resolve($attribute, app());

    expect($currentOrganization)
        ->toBeInstanceOf(Organization::class)
        ->and($currentOrganization->id)
        ->toBe($organization->id);

});
