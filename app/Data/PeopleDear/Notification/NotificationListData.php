<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Notification;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapOutputName(CamelCaseMapper::class)]
final class NotificationListData extends Data
{
    /**
     * @param  array<NotificationData>  $notifications
     */
    public function __construct(
        #[DataCollectionOf(NotificationData::class)]
        public readonly array $notifications,
        public readonly int $unread_count,
        public readonly int $current_page,
        public readonly int $last_page,
        public readonly int $total,
    ) {}
}
