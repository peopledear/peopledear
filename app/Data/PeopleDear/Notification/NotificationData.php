<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Notification;

use App\Models\Notification;
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
        public readonly string $created_ago
    ) {}

    public static function fromModel(Notification $notification): self
    {
        return new self(
            id: $notification->id,
            type: $notification->type,
            title: $notification->data['title'] ?? '',
            message: $notification->data['message'] ?? '',
            action_url: $notification->data['action_url'] ?? null,
            read_at: $notification->read_at,
            created_at: $notification->created_at,
            created_ago: $notification->created_ago
        );
    }
}
