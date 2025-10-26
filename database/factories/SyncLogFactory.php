<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SyncJobType;
use App\Enums\SyncLogStatus;
use App\Models\Organization;
use App\Models\SyncLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SyncLog>
 */
final class SyncLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'synced_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'organization_id' => Organization::factory(),
            'job_type' => SyncJobType::HolidaySync,
            'status' => SyncLogStatus::Success,
            'records_synced_count' => fake()->numberBetween(0, 100),
            'error_message' => null,
            'metadata' => null,
        ];
    }

    public function failed(): self
    {
        return $this->state(fn (array $attributes): array => [
            'status' => SyncLogStatus::Failed,
            'records_synced_count' => 0,
            'error_message' => fake()->sentence(),
        ]);
    }

    public function partial(): self
    {
        return $this->state(fn (array $attributes): array => [
            'status' => SyncLogStatus::Partial,
            'records_synced_count' => fake()->numberBetween(1, 50),
            'error_message' => fake()->sentence(),
        ]);
    }
}
