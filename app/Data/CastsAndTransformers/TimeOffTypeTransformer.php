<?php

declare(strict_types=1);

namespace App\Data\CastsAndTransformers;

use App\Data\PeopleDear\TimeOffType\TimeOffTypeData;
use App\Enums\PeopleDear\TimeOffType;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

final class TimeOffTypeTransformer implements Transformer
{
    /**
     * @param  TimeOffType  $value
     */
    public function transform(DataProperty $property, mixed $value, TransformationContext $context): TimeOffTypeData
    {
        $icon = $value->icon();

        return new TimeOffTypeData(
            type: $value->value,
            label: $value->label(),
            icon: $icon->value,
            color: $value->color(),
        );
    }
}
