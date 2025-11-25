<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PeopleDear\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
final class UserFactory extends Factory
{
    private static ?string $password = null;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => self::$password ??= 'password',
            'remember_token' => Str::random(10),
            'two_factor_secret' => Str::random(10),
            'two_factor_recovery_codes' => Str::random(10),
            'two_factor_confirmed_at' => now(),
        ];
    }

    public function unverified(): self
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }

    public function withoutTwoFactor(): self
    {
        return $this->state(fn (array $attributes): array => [
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
    }

    public function peopleManager(): self
    {
        return $this->afterCreating(static function (User $user): void {
            $user->assignRole(UserRole::PeopleManager);
        });
    }

    /**
     * Create users with proper, realistic names using a sequence.
     */
    public function withProperNames(): self
    {
        return $this->sequence(
            ['name' => 'David Kim', 'email' => 'david.kim@peopledear.test'],
            ['name' => 'Jessica Martinez', 'email' => 'jessica.martinez@peopledear.test'],
            ['name' => 'Christopher Lee', 'email' => 'christopher.lee@peopledear.test'],
            ['name' => 'Amanda Garcia', 'email' => 'amanda.garcia@peopledear.test'],
            ['name' => 'Daniel Brown', 'email' => 'daniel.brown@peopledear.test'],
            ['name' => 'Rachel Patel', 'email' => 'rachel.patel@peopledear.test'],
            ['name' => 'Andrew Taylor', 'email' => 'andrew.taylor@peopledear.test'],
            ['name' => 'Melissa Nguyen', 'email' => 'melissa.nguyen@peopledear.test'],
            ['name' => 'Robert Johnson', 'email' => 'robert.johnson@peopledear.test'],
            ['name' => 'Nicole Adams', 'email' => 'nicole.adams@peopledear.test'],
            ['name' => "Kevin O'Brien", 'email' => 'kevin.obrien@peopledear.test'],
            ['name' => 'Stephanie Wright', 'email' => 'stephanie.wright@peopledear.test'],
        );
    }
}
