<?php

declare(strict_types=1);

use App\Contracts\TimeOffTypeProcessor;
use App\Contracts\TimeOffTypeValidator;
use App\Enums\PeopleDear\TimeOffType;
use App\Processors\TimeOffType\BereavementProcessor;
use App\Processors\TimeOffType\PersonalDayProcessor;
use App\Processors\TimeOffType\SickLeaveProcessor;
use App\Processors\TimeOffType\VacationProcessor;
use App\Registries\TimeOffTypeRegistry;
use App\Validators\TimeOffType\BereavementValidator;
use App\Validators\TimeOffType\PersonalDayValidator;
use App\Validators\TimeOffType\SickLeaveValidator;
use App\Validators\TimeOffType\VacationValidator;

beforeEach(function (): void {
    $this->registry = app(TimeOffTypeRegistry::class);
});

test('returns vacation processor for vacation type', function (): void {
    $processor = $this->registry->getProcessor(TimeOffType::Vacation);

    expect($processor)
        ->toBeInstanceOf(TimeOffTypeProcessor::class)
        ->toBeInstanceOf(VacationProcessor::class);
});

test('returns sick leave processor for sick leave type', function (): void {
    $processor = $this->registry->getProcessor(TimeOffType::SickLeave);

    expect($processor)
        ->toBeInstanceOf(TimeOffTypeProcessor::class)
        ->toBeInstanceOf(SickLeaveProcessor::class);
});

test('returns personal day processor for personal day type', function (): void {
    $processor = $this->registry->getProcessor(TimeOffType::PersonalDay);

    expect($processor)
        ->toBeInstanceOf(TimeOffTypeProcessor::class)
        ->toBeInstanceOf(PersonalDayProcessor::class);
});

test('returns bereavement processor for bereavement type', function (): void {
    $processor = $this->registry->getProcessor(TimeOffType::Bereavement);

    expect($processor)
        ->toBeInstanceOf(TimeOffTypeProcessor::class)
        ->toBeInstanceOf(BereavementProcessor::class);
});

test('returns vacation validator for vacation type', function (): void {
    $validator = $this->registry->getValidator(TimeOffType::Vacation);

    expect($validator)
        ->toBeInstanceOf(TimeOffTypeValidator::class)
        ->toBeInstanceOf(VacationValidator::class);
});

test('returns sick leave validator for sick leave type', function (): void {
    $validator = $this->registry->getValidator(TimeOffType::SickLeave);

    expect($validator)
        ->toBeInstanceOf(TimeOffTypeValidator::class)
        ->toBeInstanceOf(SickLeaveValidator::class);
});

test('returns personal day validator for personal day type', function (): void {
    $validator = $this->registry->getValidator(TimeOffType::PersonalDay);

    expect($validator)
        ->toBeInstanceOf(TimeOffTypeValidator::class)
        ->toBeInstanceOf(PersonalDayValidator::class);
});

test('returns bereavement validator for bereavement type', function (): void {
    $validator = $this->registry->getValidator(TimeOffType::Bereavement);

    expect($validator)
        ->toBeInstanceOf(TimeOffTypeValidator::class)
        ->toBeInstanceOf(BereavementValidator::class);
});
