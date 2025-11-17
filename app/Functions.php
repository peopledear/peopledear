<?php

declare(strict_types=1);

namespace App;

use App\Enums\Support\SessionKey;
use App\Models\Organization;
use Illuminate\Support\Facades\Session;

use function function_exists;

if (! function_exists('organization')) {

    function organization(): ?Organization
    {
        return Organization::query()
            ->where('id', Session::get(SessionKey::CurrentOrganization->value))
            ->first();
    }

}
