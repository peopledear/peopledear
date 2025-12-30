<?php

declare(strict_types=1);

use App\Actions\Organization\MakeOrganizationSlug;
use App\Models\Organization;

beforeEach(function (): void {

    $this->action = resolve(MakeOrganizationSlug::class);

});

test('makes a unique slug if slug already exists',
    /**
     * @throws Throwable
     */
    function (): void {

        Organization::factory()
            ->createQuietly([
                'name' => $name = 'Minimal Org',
                'slug' => Illuminate\Support\Str::slug($name),
            ])
            ->fresh();

        $slug = $this->action->handle($name);

        expect($slug)
            ->toStartWith('minimal-org-');

    });

test('generates slug from name',
    /**
     * @throws Throwable
     */
    function (): void {

        $name = 'Test Organization Name';
        $expectedSlug = 'test-organization-name';

        $slug = $this->action->handle($name);

        expect($slug)->toBe($expectedSlug);

    });
