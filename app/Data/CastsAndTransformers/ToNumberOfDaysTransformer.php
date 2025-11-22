<?php

declare(strict_types=1);

namespace App\Data\CastsAndTransformers;

use Illuminate\Support\Number;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

final class ToNumberOfDaysTransformer implements Cast
{
    /**
     * @param  array<string, mixed>  $properties
     * @param  CreationContext<\Spatie\LaravelData\Data>  $context
     */
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): string
    {
        assert(is_int($value));

        $divided = $value / 100;

        return (string) Number::format($divided, maxPrecision: 1);
    }
}
