<?php

declare(strict_types=1);

namespace App\Data\CastsAndTransformers;

use App\Data\IconData;
use App\Enums\Icon;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

final class AsIconDataTransformer implements Transformer
{
    /**
     * @param  Icon  $value
     */
    public function transform(DataProperty $property, mixed $value, TransformationContext $context): IconData
    {
        return new IconData(
            value: $value->value,
            name: $value->name,
            icon: $value->icon(),
            label: $value->label(),
        );
    }
}
