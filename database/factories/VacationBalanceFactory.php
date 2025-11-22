<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Organization;
use App\Models\VacationBalance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VacationBalance>
 */
final class VacationBalanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'employee_id' => Employee::factory(),
            'year' => now()->year,
            'from_last_year' => fake()->numberBetween(2, 10) * 50,
            'accrued' => fake()->numberBetween(44, 50) * 50,
            'taken' => fake()->numberBetween(2, 30) * 50,
        ];
    }
}
