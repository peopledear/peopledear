<?php

declare(strict_types=1);

namespace App\Exceptions\Domain;

use App\Support\Exceptions\DomainException;

final class LocationAlreadyExistsException extends DomainException
{
    public static function headquartersInCountry(
        string $organization,
        string $country
    ): self {
        return new self(
            sprintf("A headquarters for organization '%s' already exists in country '%s'.", $organization, $country)
        );
    }
}
