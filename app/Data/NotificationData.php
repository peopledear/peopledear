<?php

declare(strict_types=1);

namespace App\Data;

use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapOutputName(CamelCaseMapper::class)]
final class NotificationData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $title,
        public readonly string $message,
        public readonly ?string $action_url,
        public readonly ?CarbonInterface $read_at,
        public readonly CarbonInterface $created_at,
    ) {}
}
