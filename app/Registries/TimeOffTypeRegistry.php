<?php

declare(strict_types=1);

namespace App\Registries;

use App\Contracts\TimeOffTypeProcessor;
use App\Contracts\TimeOffTypeValidator;
use App\Enums\PeopleDear\TimeOffType;
use App\Processors\TimeOffType\BereavementProcessor;
use App\Processors\TimeOffType\PersonalDayProcessor;
use App\Processors\TimeOffType\SickLeaveProcessor;
use App\Processors\TimeOffType\VacationProcessor;
use App\Validators\TimeOffType\BereavementValidator;
use App\Validators\TimeOffType\PersonalDayValidator;
use App\Validators\TimeOffType\SickLeaveValidator;
use App\Validators\TimeOffType\VacationValidator;

final readonly class TimeOffTypeRegistry
{
    public function __construct(
        private VacationProcessor $vacationProcessor,
        private SickLeaveProcessor $sickLeaveProcessor,
        private PersonalDayProcessor $personalDayProcessor,
        private BereavementProcessor $bereavementProcessor,
        private VacationValidator $vacationValidator,
        private SickLeaveValidator $sickLeaveValidator,
        private PersonalDayValidator $personalDayValidator,
        private BereavementValidator $bereavementValidator,
    ) {}

    public function getProcessor(TimeOffType $type): TimeOffTypeProcessor
    {
        return match ($type) {
            TimeOffType::Vacation => $this->vacationProcessor,
            TimeOffType::SickLeave => $this->sickLeaveProcessor,
            TimeOffType::PersonalDay => $this->personalDayProcessor,
            TimeOffType::Bereavement => $this->bereavementProcessor,
        };
    }

    public function getValidator(TimeOffType $type): TimeOffTypeValidator
    {
        return match ($type) {
            TimeOffType::Vacation => $this->vacationValidator,
            TimeOffType::SickLeave => $this->sickLeaveValidator,
            TimeOffType::PersonalDay => $this->personalDayValidator,
            TimeOffType::Bereavement => $this->bereavementValidator,
        };
    }
}
