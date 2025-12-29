<?php

declare(strict_types=1);

namespace App\Casts;

use App\Enums\PeopleDear\TimeOffUnit;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use JsonException;

use function array_map;
use function json_encode;

/**
 * @implements CastsAttributes<TimeOffUnit[], TimeOffUnit[]>
 */
final class AsArrayOfTimeOffUnit implements CastsAttributes
{
    /**
     * @param  string  $value
     * @param  array<string, mixed>  $attributes
     * @return TimeOffUnit[]
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {

        /** @var array<int, int> $decoded */
        $decoded = json_decode($value, true);

        return array_map(TimeOffUnit::from(...), $decoded);

    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  TimeOffUnit[]|int[]  $value
     *
     * @throws JsonException
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {

        /** @var array<int, int> $value */
        $value = array_map(fn (TimeOffUnit|int $unit): int => is_int($unit) ? TimeOffUnit::from($unit)->value : $unit->value, $value);

        return json_encode($value, JSON_THROW_ON_ERROR);
    }
}
