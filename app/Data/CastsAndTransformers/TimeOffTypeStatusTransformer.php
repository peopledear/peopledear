<?php

declare(strict_types=1);

namespace App\Data\CastsAndTransformers;

use App\Data\PeopleDear\TimeOffTypeStatus\TimeOffTypeStatusData;
use App\Enums\TimeOffTypeStatus;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

final class TimeOffTypeStatusTransformer implements Transformer
{
    /**
     * @param  TimeOffTypeStatus  $value
     */
    public function transform(DataProperty $property, mixed $value, TransformationContext $context): TimeOffTypeStatusData
    {
        return new TimeOffTypeStatusData(
            value: $value->value,
            label: $value->label(),
            color: $value->color(),
        );
    }
}
