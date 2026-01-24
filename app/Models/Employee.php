<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Addressable;
use App\Enums\EmploymentStatus;
use App\Models\Concerns\HasAddress;
use Carbon\CarbonImmutable;
use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sprout\Attributes\TenantRelation;
use Sprout\Database\Eloquent\Concerns\BelongsToTenant;

/**
 * @property-read string $id
 * @property-read string $organization_id
 * @property-read string|null $location_id
 * @property-read string|null $user_id
 * @property-read string|null $manager_id
 * @property-read string $name
 * @property-read string|null $email
 * @property-read string|null $phone
 * @property-read string $employee_number
 * @property-read string|null $job_title
 * @property-read CarbonImmutable|null $hire_date
 * @property-read EmploymentStatus $employment_status
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Organization $organization
 * @property-read Location|null $location
 * @property-read Address $address
 * @property-read User|null $user
 * @property-read Employee|null $manager
 * @property-read Collection<int, Employee> $directReports
 */
final class Employee extends Model implements Addressable
{
    use BelongsToTenant;
    use HasAddress;

    /** @use HasFactory<EmployeeFactory> */
    use HasFactory;

    use HasUuids;

    public function casts(): array
    {
        return [
            'id' => 'string',
            'organization_id' => 'string',
            'location_id' => 'string',
            'user_id' => 'string',
            'manager_id' => 'string',
            'name' => 'string',
            'email' => 'string',
            'phone' => 'string',
            'employee_number' => 'string',
            'job_title' => 'string',
            'hire_date' => 'immutable_datetime',
            'employment_status' => EmploymentStatus::class,
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

    /** @return BelongsTo<Location, $this> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Employee, $this> */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(self::class, 'manager_id');
    }

    /** @return HasMany<Employee, $this> */
    public function directReports(): HasMany
    {
        return $this->hasMany(self::class, 'manager_id');
    }

    /**
     * @return HasMany<VacationBalance, $this>
     */
    public function vacationBalances(): HasMany
    {
        return $this->hasMany(VacationBalance::class, 'employee_id', 'id');
    }
}
