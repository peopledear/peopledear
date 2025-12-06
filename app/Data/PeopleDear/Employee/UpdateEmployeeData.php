<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Employee;

use App\Enums\PeopleDear\EmploymentStatus;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * @method array<string, mixed> toArray()
 */
final class UpdateEmployeeData extends Data
{
    public function __construct(
        public readonly string|Optional $name,
        public readonly string|Optional $employee_number,
        public readonly EmploymentStatus|Optional $employment_status,
        public readonly string|Optional|null $email,
        public readonly string|Optional|null $phone,
        public readonly string|Optional|null $job_title,
        public readonly CarbonImmutable|Optional|null $hire_date,
        public readonly string|Optional|null $office_id,
        public readonly string|Optional|null $user_id,
    ) {}
}
