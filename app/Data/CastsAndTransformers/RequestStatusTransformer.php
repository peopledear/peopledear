<?php

declare(strict_types=1);

namespace App\Data\CastsAndTransformers;

use App\Data\PeopleDear\RequestStatus\RequestStatusData;
use App\Enums\RequestStatus;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

final class RequestStatusTransformer implements Transformer
{
    /**
     * @param  RequestStatus  $value
     */
    public function transform(DataProperty $property, mixed $value, TransformationContext $context): RequestStatusData
    {
        return new RequestStatusData(
            value: $value->value,
            label: $value->label(),
            icon: $value->icon()->value,
            color: $value->color(),
        );
    }
}
