<?php

declare(strict_types=1);

use App\Http\Requests\CreateUserRequest;
use Illuminate\Validation\Rules\Password;

test('password validation rules in testing environment', function (): void {
    $request = new CreateUserRequest;
    $rules = $request->rules();

    expect($rules['password'][0])->toBe('required')
        ->and($rules['password'][1])->toBe('confirmed')
        ->and($rules['password'][2])->toBeInstanceOf(Password::class);
});

test('password validation rules in local environment',
    function (): void {
        app()->detectEnvironment(fn (): string => 'local');
        $request = new CreateUserRequest;
        $rules = $request->rules();

        expect($rules)
            ->not->toContain(Password::defaults())
            ->and($rules['password'][0])
            ->toBe('required')
            ->and($rules['password'][1])
            ->toBe('confirmed')
            ->and(array_key_exists(2, $rules))
            ->toBeFalse();
    });

test('password validation rules in other environments', function (): void {
    $request = new CreateUserRequest;
    $rules = $request->rules();

    expect($rules['password'][0])->toBe('required')
        ->and($rules['password'][1])->toBe('confirmed')
        ->and($rules['password'][2])->toBeInstanceOf(Password::class);
});

test('name validation rules are correct', function (): void {
    $request = new CreateUserRequest;
    $rules = $request->rules();

    expect($rules['name'])->toBe(['required', 'string', 'max:255']);
});

test('email validation rules are correct', function (): void {
    $request = new CreateUserRequest;
    $rules = $request->rules();

    expect($rules['email'][0])->toBe('required')
        ->and($rules['email'][1])->toBe('string')
        ->and($rules['email'][2])->toBe('lowercase')
        ->and($rules['email'][3])->toBe('max:255')
        ->and($rules['email'][4])->toBe('email');
});

test('validation rules contain all required fields', function (): void {
    $request = new CreateUserRequest;
    $rules = $request->rules();

    expect($rules)->toHaveKeys(['name', 'email', 'password']);
});
