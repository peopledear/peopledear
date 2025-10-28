<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Enums\Support\SessionKey;
use App\Models\Organization;
use Illuminate\Contracts\Session\Session;

final readonly class SetCurrentOrganization
{
    public function __construct(private Session $session)
    {
    }

    /**
     * Execute the action.
     */
    public function handle(Organization $organization): void
    {
        $this->session->put(SessionKey::CurrentOrganization->value, $organization->id);
    }
}
