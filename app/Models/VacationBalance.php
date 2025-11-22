<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToEmployee;
use App\Models\Concerns\BelongsToOrganization;
use App\Models\Scopes\OrganizationScope;
use Database\Factories\VacationBalanceFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property-read int $organization_id
 * @property-read int $employee_id
 * @property-read int $year
 * @property-read int $from_last_year
 * @property-read int $accrued
 * @property-read int $taken
 * @property-read int $remaining
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Organization $organization
 * @property-read Employee $employee
 */
#[ScopedBy([OrganizationScope::class])]
final class VacationBalance extends Model
{
    use BelongsToEmployee;
    use BelongsToOrganization;

    /** @use HasFactory<VacationBalanceFactory> */
    use HasFactory;

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
