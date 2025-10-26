<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\EmploymentStatus;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

/**
 * @method array<string, mixed> toArray()
 */
final class CreateEmployeeData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $employee_number,
        public readonly EmploymentStatus $employment_status,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $job_title,
        public readonly ?Carbon $hire_date,
        public readonly ?int $office_id,
        public readonly ?int $user_id,
    ) {}
}
