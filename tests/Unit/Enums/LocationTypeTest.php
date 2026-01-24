<?php

declare(strict_types=1);

use App\Enums\Icon;
use App\Enums\LocationType;

test('has all expected cases',
    /**
     * @throws Throwable
     */
    function (): void {
        $cases = LocationType::cases();

        expect($cases)
            ->toHaveCount(7)
            ->and(array_map(fn (LocationType $case) => $case->name, $cases))
            ->toBe([
                'Headquarters',
                'Branch',
                'Warehouse',
                'Store',
                'Factory',
                'Remote',
                'Coworking',
            ]);
    });

test('has correct values',
    /**
     * @throws Throwable
     */
    function (): void {
        expect(LocationType::Headquarters->value)->toBe(1)
            ->and(LocationType::Branch->value)->toBe(2)
            ->and(LocationType::Warehouse->value)->toBe(3)
            ->and(LocationType::Store->value)->toBe(4)
            ->and(LocationType::Factory->value)->toBe(5)
            ->and(LocationType::Remote->value)->toBe(6)
            ->and(LocationType::Coworking->value)->toBe(7);
    });

test('has correct labels',
    /**
     * @throws Throwable
     */
    function (): void {
        expect(LocationType::Headquarters->label())->toBe(__('Headquarters'))
            ->and(LocationType::Branch->label())->toBe(__('Branch'))
            ->and(LocationType::Warehouse->label())->toBe(__('Warehouse'))
            ->and(LocationType::Store->label())->toBe(__('Store'))
            ->and(LocationType::Factory->label())->toBe(__('Factory'))
            ->and(LocationType::Remote->label())->toBe(__('Remote'))
            ->and(LocationType::Coworking->label())->toBe(__('Coworking'));
    });

test('has correct icons',
    /**
     * @throws Throwable
     */
    function (): void {
        expect(LocationType::Headquarters->icon())->toBe(Icon::LucideBuilding)
            ->and(LocationType::Branch->icon())->toBe(Icon::LucideBuilding)
            ->and(LocationType::Warehouse->icon())->toBe(Icon::LucideGavel)
            ->and(LocationType::Store->icon())->toBe(Icon::LucideUtensils)
            ->and(LocationType::Factory->icon())->toBe(Icon::LucideBriefcase)
            ->and(LocationType::Remote->icon())->toBe(Icon::LucideGlobe)
            ->and(LocationType::Coworking->icon())->toBe(Icon::LucideUsers);
    });

test('options method returns correct array',
    /**
     * @throws Throwable
     */
    function (): void {
        $options = LocationType::options();

        expect($options)
            ->toBeArray()
            ->toHaveCount(7)
            ->and($options[1])->toBe(__('Headquarters'))
            ->and($options[2])->toBe(__('Branch'))
            ->and($options[7])->toBe(__('Coworking'));
    });
