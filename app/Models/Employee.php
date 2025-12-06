<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PeopleDear\EmploymentStatus;
use App\Models\Concerns\BelongsToOrganization;
use App\Models\Scopes\OrganizationScope;
use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property-read string $id
 * @property-read string $organization_id
 * @property-read string|null $office_id
 * @property-read string|null $user_id
 * @property-read string|null $manager_id
 * @property-read string $name
 * @property-read string|null $email
 * @property-read string|null $phone
 * @property-read string $employee_number
 * @property-read string|null $job_title
 * @property-read Carbon|null $hire_date
 * @property-read EmploymentStatus $employment_status
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Organization $organization
 * @property-read Office|null $office
 * @property-read User|null $user
 * @property-read Employee|null $manager
 * @property-read Collection<int, Employee> $directReports
 */
#[ScopedBy([OrganizationScope::class])]
final class Employee extends Model
{
    use BelongsToOrganization;

    /** @use HasFactory<EmployeeFactory> */
    use HasFactory;

    use HasUuids;

    public function casts(): array
    {
        return [
            'id' => 'string',
            'organization_id' => 'string',
            'office_id' => 'string',
            'user_id' => 'string',
            'manager_id' => 'string',
            'name' => 'string',
            'email' => 'string',
            'phone' => 'string',
            'employee_number' => 'string',
            'job_title' => 'string',
            'hire_date' => 'date',
            'employment_status' => EmploymentStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Office, $this> */
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
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
