<?php

declare(strict_types=1);

use App\Data\PeopleDear\TimeOffType\TimeOffTypeBalanceConfigData;
use App\Enums\BalanceType;
use App\Enums\CarryOverType;
use App\Enums\Icon;
use App\Enums\TimeOffUnit;

return [
    'time_off_types' => [
        'vacation' => [
            'name' => 'Vacation',
            'is_system' => true,
            'allowed_units' => [
                TimeOffUnit::Day,
                TimeOffUnit::HalfDay,
            ],
            'icon' => Icon::LucidePlaneTakeoff,
            'color' => '#00FF00',

            'requires_approval' => true,
            'requires_justification' => false,
            'requires_justification_document' => false,
            'balance_mode' => BalanceType::Annual,
            'balance_config' => new TimeOffTypeBalanceConfigData(
                accrualDaysPerYear: 22,
                carryOverType: CarryOverType::Limited,
                carryOverDaysLimit: 5,
            ),
            'description' => 'Annual vacation time off',
        ],
        'sick_leave' => [
            'name' => 'Sick Leave',
            'is_system' => true,
            'allowed_units' => [
                TimeOffUnit::Day,
                TimeOffUnit::HalfDay,
                TimeOffUnit::Hour,
            ],
            'icon' => Icon::LucideHeartPulse,
            'color' => '#FF0000',

            'requires_approval' => false,
            'requires_justification' => true,
            'requires_justification_document' => true,
            'balance_mode' => BalanceType::None,
            'description' => 'Sick leave for employees',
        ],
    ],
];
