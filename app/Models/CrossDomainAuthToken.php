<?php

declare(strict_types=1);

namespace App\Models;

use App\Queries\CrossDomainAuthTokenQuery;
use Carbon\CarbonInterface;
use Database\Factories\CrossDomainAuthTokenFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read string $id
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read string $organization_id
 * @property-read string $user_id
 * @property-read string $nonce
 * @property-read string $intended
 * @property-read CarbonInterface $expires_at
 * @property CarbonInterface|null $used_at
 * @property-read Organization $organization
 * @property-read User $user
 */
final class CrossDomainAuthToken extends Model
{
    /** @use HasFactory<CrossDomainAuthTokenFactory> */
    use HasFactory;

    use HasUuids;
    use MassPrunable;

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    public function markAsUsed(): void
    {
        $this->update(['used_at' => now()]);
    }

    /**
     * @return Builder<self>
     */
    public function prunable(): Builder
    {
        return resolve(CrossDomainAuthTokenQuery::class)()
            ->builder()
            ->where('expires_at', '<', now())
            ->orWhereNotNull('used_at');
    }
}
