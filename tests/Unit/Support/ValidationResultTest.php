<?php

declare(strict_types=1);

use App\Support\ValidationResult;

covers(ValidationResult::class);

test('pass creates valid result with no errors', function (): void {
    $result = ValidationResult::pass();

    expect($result->valid)->toBeTrue()
        ->and($result->errors)->toBeEmpty();
});

test('fail creates invalid result with errors', function (): void {
    $errors = [
        'field' => 'Error message',
        'another_field' => 'Another error',
    ];

    $result = ValidationResult::fail($errors);

    expect($result->valid)->toBeFalse()
        ->and($result->errors)->toBe($errors);
});

test('constructor sets properties correctly', function (): void {
    $result = new ValidationResult(
        valid: false,
        errors: ['test' => 'Test error'],
    );

    expect($result->valid)->toBeFalse()
        ->and($result->errors)->toHaveKey('test');
});
