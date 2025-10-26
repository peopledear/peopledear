<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Queries\OrganizationQuery;
use Illuminate\Database\Eloquent\Builder;

beforeEach(function (): void {
    $this->query = new OrganizationQuery;
});

test('returns eloquent builder instance', function (): void {
    $builder = $this->query->builder();

    expect($builder)->toBeInstanceOf(Builder::class);
});

test('builder returns organization query builder', function (): void {
    $builder = $this->query->builder();

    expect($builder->getModel())->toBeInstanceOf(Organization::class);
});

test('can retrieve all organizations using builder', function (): void {
    /** @var Organization $org1 */
    $org1 = Organization::factory()->createQuietly([
        'name' => 'First Organization',
    ]);

    /** @var Organization $org2 */
    $org2 = Organization::factory()->createQuietly([
        'name' => 'Second Organization',
    ]);

    /** @var Organization $org3 */
    $org3 = Organization::factory()->createQuietly([
        'name' => 'Third Organization',
    ]);

    $organizations = $this->query->builder()->get();

    expect($organizations)
        ->toHaveCount(3)
        ->first()->name->toBe('First Organization')
        ->and($organizations->last())->name->toBe('Third Organization');
});

test('can filter organizations using builder', function (): void {
    /** @var Organization $targetOrg */
    $targetOrg = Organization::factory()->createQuietly([
        'name' => 'Target Organization',
    ]);

    Organization::factory()->createQuietly([
        'name' => 'Other Organization',
    ]);

    $result = $this->query->builder()
        ->where('name', 'Target Organization')
        ->first();

    expect($result)
        ->not->toBeNull()
        ->id->toBe($targetOrg->id)
        ->and($result)->name->toBe('Target Organization');
});

test('can order organizations using builder', function (): void {
    Organization::factory()->createQuietly(['name' => 'Zebra Org']);
    Organization::factory()->createQuietly(['name' => 'Alpha Org']);
    Organization::factory()->createQuietly(['name' => 'Beta Org']);

    $organizations = $this->query->builder()
        ->orderBy('name')
        ->get();

    expect($organizations)
        ->toHaveCount(3)
        ->first()->name->toBe('Alpha Org')
        ->and($organizations->last())->name->toBe('Zebra Org');
});

test('can limit organizations using builder', function (): void {
    Organization::factory()->count(5)->createQuietly();

    $organizations = $this->query->builder()
        ->limit(2)
        ->get();

    expect($organizations)->toHaveCount(2);
});

test('builder supports relationship eager loading', function (): void {
    /** @var Organization $org */
    $org = Organization::factory()->createQuietly();

    $result = $this->query->builder()
        ->with('offices')
        ->where('id', $org->id)
        ->first();

    expect($result)
        ->not->toBeNull()
        ->relationLoaded('offices')->toBeTrue();
});

test('returns empty collection when no organizations exist', function (): void {
    $organizations = $this->query->builder()->get();

    expect($organizations)
        ->toBeEmpty()
        ->toHaveCount(0);
});
