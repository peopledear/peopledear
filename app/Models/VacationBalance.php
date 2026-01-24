<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToEmployee;
use Carbon\CarbonImmutable;
use Database\Factories\VacationBalanceFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sprout\Attributes\TenantRelation;
use Sprout\Database\Eloquent\Concerns\BelongsToTenant;

/**
 * @property-read string $id
 * @property-read string $organization_id
 * @property-read string $employee_id
 * @property-read int $year
 * @property-read int $from_last_year
 * @property-read int $accrued
 * @property-read int $taken
 * @property-read int $remaining
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Organization $organization
 * @property-read Employee $employee
 */
final class VacationBalance extends Model
{
    use BelongsToEmployee;
    use BelongsToTenant;

    /** @use HasFactory<VacationBalanceFactory> */
    use HasFactory;

    use HasUuids;

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'organization_id' => 'string',
            'employee_id' => 'string',
            'year' => 'integer',
            'from_last_year' => 'integer',
            'accrued' => 'integer',
            'taken' => 'integer',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<Organization, $this> */
    #[TenantRelation]
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return Attribute<int, never>
     */
    protected function yearBalance(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->from_last_year <= $this->taken
                ? $this->accrued + $this->from_last_year - $this->taken
                : $this->accrued,
        );
    }

    /**
     * @return Attribute<int, never>
     */
    protected function lastYearBalance(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->from_last_year <= $this->taken
                ? 0
                : $this->from_last_year - $this->taken,
        );
    }

    /**
     * @return Attribute<int, never>
     */
    protected function remaining(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->from_last_year + $this->accrued - $this->taken,
        );
    }
}
