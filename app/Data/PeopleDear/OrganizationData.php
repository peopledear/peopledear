<?php

declare(strict_types=1);

namespace App\Data\PeopleDear;

use DateTimeInterface;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapOutputName(CamelCaseMapper::class)]
final class OrganizationData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $identifier,
        public ?string $vat_number,
        public ?string $ssn,
        public ?string $phone,
        public string $resource_key,
        public DateTimeInterface $created_at,
        public DateTimeInterface $updated_at,

    ) {}

}
