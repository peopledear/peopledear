<?php

declare(strict_types=1);

use App\Actions\SetCurrentOrganization;
use App\Enums\SessionKey;
use App\Models\Organization;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

beforeEach(function (): void {
    $this->action = app(SetCurrentOrganization::class);
});

test('organization id is stored in the session',
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    function (): void {
        /** @var Organization $organization */
        $organization = Organization::factory()
            ->createQuietly();
        $this->action->handle($organization);

        expect(session()->get(SessionKey::CurrentOrganization->value))
            ->toBe($organization->id);

    });
