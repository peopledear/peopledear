<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EmploymentStatus;
use App\Models\Employee;
use App\Models\Office;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
final class EmployeeFactory extends Factory
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
            'office_id' => fake()->optional()->randomElement([null, Office::factory()]),
            'user_id' => fake()->optional()->randomElement([null, User::factory()]),
            'name' => fake()->name(),
            'email' => fake()->boolean(80) ? fake()->unique()->safeEmail() : null,
            'phone' => fake()->optional()->phoneNumber(),
            'employee_number' => fake()->unique()->numerify('EMP-####'),
            'job_title' => fake()->boolean(80) ? fake()->jobTitle() : null,
            'hire_date' => fake()->boolean(80) ? fake()->dateTimeBetween('-5 years', 'now') : null,
            'employment_status' => fake()->randomElement(EmploymentStatus::cases()),
        ];
    }
}
