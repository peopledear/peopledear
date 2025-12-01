<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Notification;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapOutputName(CamelCaseMapper::class)]
final class NotificationListData extends Data
{
    /**
     * @param  DataCollection<int, NotificationData>  $notifications
     */
    public function __construct(
        #[DataCollectionOf(NotificationData::class)]
        public readonly DataCollection $notifications,
        public readonly int $unread,
        public readonly int $total,
    ) {}

    /**
     * @param  Collection<int, Notification>  $notifications
     */
    public static function fromEloquentCollection(Collection $notifications): self
    {

        $notifications = NotificationData::collect(
            $notifications->map(fn (Notification $notification): NotificationData => NotificationData::fromModel($notification)),
            DataCollection::class
        );

        return new self(
            notifications: $notifications,
            unread: $notifications->toCollection()->reject(fn (NotificationData $n): bool => $n->read_at instanceof \Carbon\CarbonInterface)->count(),
            total: $notifications->count(),
        );
    }
}
