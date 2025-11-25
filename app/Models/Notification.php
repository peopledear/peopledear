<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\OrganizationScope;
use App\Models\Scopes\SetOrganizationScope;
use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Carbon;

/**
 * @property-read string $id
 * @property-read string $type
 * @property-read string $notifiable_type
 * @property-read int $notifiable_id
 * @property-read array{title?: string, message?: string, action_url?: string|null} $data
 * @property-read Carbon|null $read_at
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read string $created_ago
 * @property-read int|null $organization_id
 * @property-read Organization|null $organization
 */
#[ScopedBy([OrganizationScope::class, SetOrganizationScope::class])]
final class Notification extends DatabaseNotification
{
    /** @use HasFactory<NotificationFactory> */
    use HasFactory;

    use MassPrunable;

    /**
     * Get the organization that owns the notification.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the prunable model query.
     *
     * @return Builder<Notification>
     */
    public function prunable(): Builder
    {
        return self::query()
            ->withoutGlobalScope(OrganizationScope::class)
            ->where('created_at', '<=', now()->subDays(90));
    }

    /**
     * @return Attribute<string, never>
     */
    protected function createdAgo(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->created_at->diffForHumans(),
        );
    }
}
