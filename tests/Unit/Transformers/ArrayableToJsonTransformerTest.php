<?php

declare(strict_types=1);

use App\Transformers\ArrayableToJsonTransformer;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;

test('it transforms array to json string', function (): void {
    $transformer = new ArrayableToJsonTransformer;
    $property = Mockery::mock(DataProperty::class);
    $context = Mockery::mock(TransformationContext::class);

    $value = ['key' => 'value', 'another' => 'data'];
    $result = $transformer->transform($property, $value, $context);

    expect($result)
        ->toBeString()
        ->toBe(json_encode($value))
        ->toBe('{"key":"value","another":"data"}');
});

test('it returns non-array values unchanged', function (): void {
    $transformer = new ArrayableToJsonTransformer;
    $property = Mockery::mock(DataProperty::class);
    $context = Mockery::mock(TransformationContext::class);

    $stringValue = 'test string';
    $result = $transformer->transform($property, $stringValue, $context);

    expect($result)->toBe($stringValue);

    $intValue = 42;
    $result = $transformer->transform($property, $intValue, $context);

    expect($result)->toBe($intValue);

    $nullValue = null;
    $result = $transformer->transform($property, $nullValue, $context);

    expect($result)->toBeNull();
});
