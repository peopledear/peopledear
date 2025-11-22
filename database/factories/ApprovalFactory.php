<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PeopleDear\RequestStatus;
use App\Models\Approval;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\TimeOffRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Approval>
 */
final class ApprovalFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => TimeOffRequest::factory(),
            'status' => RequestStatus::Pending,
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ];
    }

    public function pending(): self
    {
        return $this->state(fn (array $attributes): array => [
            'status' => RequestStatus::Pending,
        ]);
    }

    public function approved(): self
    {
        return $this->state(fn (array $attributes): array => [
            'status' => RequestStatus::Approved,
            'approved_by' => Employee::factory(),
            'approved_at' => now(),
        ]);
    }

    public function rejected(): self
    {
        return $this->state(fn (array $attributes): array => [
            'status' => RequestStatus::Rejected,
            'approved_by' => Employee::factory(),
            'approved_at' => now(),
            'rejection_reason' => fake()->sentence(),
        ]);
    }

    public function cancelled(): self
    {
        return $this->state(fn (array $attributes): array => [
            'status' => RequestStatus::Cancelled,
        ]);
    }
}
