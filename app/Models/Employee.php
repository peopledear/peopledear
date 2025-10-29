<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PeopleDear\EmploymentStatus;
use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property-read int $organization_id
 * @property-read int|null $office_id
 * @property-read int|null $user_id
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
 */
final class Employee extends Model
{
    /** @use HasFactory<EmployeeFactory> */
    use HasFactory;

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
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

    public function casts(): array
    {
        return [
            'id' => 'integer',
            'organization_id' => 'integer',
            'office_id' => 'integer',
            'user_id' => 'integer',
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
}
