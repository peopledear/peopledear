<?php

declare(strict_types=1);

use App\Data\CastsAndTransformers\TimeOffTypeStatusTransformer;
use App\Data\PeopleDear\TimeOffTypeStatus\TimeOffTypeStatusData;
use App\Enums\TimeOffTypeStatus;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;

test('transforms TimeOffTypeStatus enum to TimeOffTypeStatusData',
    /**
     * @throws Throwable
     */
    function (TimeOffTypeStatus $status, string $expectedLabel, string $expectedColor): void {
        $transformer = new TimeOffTypeStatusTransformer;

        /** @var DataProperty $property */
        $property = Mockery::mock(DataProperty::class);

        /** @var TransformationContext $context */
        $context = Mockery::mock(TransformationContext::class);

        $result = $transformer->transform($property, $status, $context);

        expect($result)
            ->toBeInstanceOf(TimeOffTypeStatusData::class)
            ->and($result->value)
            ->toBe($status->value)
            ->and($result->label)
            ->toBe($expectedLabel)
            ->and($result->color)
            ->toBe($expectedColor);

    })->with([
        [TimeOffTypeStatus::Pending, 'Pending', 'warning'],
        [TimeOffTypeStatus::Active, 'Active', 'success'],
        [TimeOffTypeStatus::Inactive, 'Inactive', 'secondary'],
    ]);
