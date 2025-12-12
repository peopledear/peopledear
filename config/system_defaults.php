<?php

use App\Data\PeopleDear\TimeOffType\TimeOffTypeBalanceConfigData;
use App\Enums\PeopleDear\CarryOverType;
use App\Enums\PeopleDear\TimeOffBalanceMode;
use App\Enums\PeopleDear\TimeOffUnit;
use App\Enums\Support\TimeOffIcon;

return [
    'time_off_types' => [
        'vacation' => [
            'name' => 'Vacation',
            'is_system' => true,
            'allowed_units' => [
                TimeOffUnit::Day,
                TimeOffUnit::HalfDay,
            ],
            'icon' => TimeOffIcon::PlaneTakeoff,
            'color' => '#00FF00',
            'is_active' => true,
            'requires_approval' => true,
            'requires_justification' => false,
            'requires_justification_document' => false,
            'balance_mode' => TimeOffBalanceMode::Annual,
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
            'icon' => TimeOffIcon::HeartPulse,
            'color' => '#FF0000',
            'is_active' => true,
            'requires_approval' => false,
            'requires_justification' => true,
            'requires_justification_document' => true,
            'balance_mode' => TimeOffBalanceMode::None,
            'description' => 'Sick leave for employees',
        ],
    ]
];
