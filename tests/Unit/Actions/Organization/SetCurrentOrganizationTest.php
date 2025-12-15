<?php

declare(strict_types=1);

use App\Actions\Organization\SetCurrentOrganization;
use App\Enums\Support\SessionKey;
use App\Models\Organization;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

beforeEach(function (): void {
    $this->action = resolve(SetCurrentOrganization::class);
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
            ->create();
        $this->action->handle($organization);

        expect(session()->get(SessionKey::CurrentOrganization->value))
            ->toBe($organization->id);

    });
