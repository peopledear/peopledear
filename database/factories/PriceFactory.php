<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Price;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Price>
 */
final class PriceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'stripe_price_id' => null,
            'interval' => fake()->randomElement(['month', 'year']),
            'amount' => fake()->randomElement([990, 1990, 2990, 4990, 9990]),
            'currency' => 'eur',
            'is_active' => true,
        ];
    }

    public function monthly(): self
    {
        return $this->state(fn (array $attributes): array => [
            'interval' => 'month',
        ]);
    }

    public function yearly(): self
    {
        return $this->state(fn (array $attributes): array => [
            'interval' => 'year',
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }

    public function synced(): self
    {
        return $this->state(fn (array $attributes): array => [
            'stripe_price_id' => 'price_'.fake()->regexify('[A-Za-z0-9]{14}'),
        ]);
    }
}
