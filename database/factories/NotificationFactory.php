<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Notification;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Notification>
 */
final class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'type' => \App\Notifications\GeneralNotification::class,
            'notifiable_type' => User::class,
            'notifiable_id' => User::factory(),
            'data' => [
                'title' => fake()->sentence(3),
                'message' => fake()->sentence(),
                'action_url' => null,
            ],
            'read_at' => null,
            'organization_id' => Organization::factory(),
        ];
    }

    public function read(): self
    {
        return $this->state(fn (array $attributes): array => [
            'read_at' => now(),
        ]);
    }
}
