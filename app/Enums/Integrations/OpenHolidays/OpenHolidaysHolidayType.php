<?php

declare(strict_types=1);

namespace App\Enums\Integrations\OpenHolidays;

use App\Enums\HolidayType;

enum OpenHolidaysHolidayType: string
{
    case Public = 'Public';
    case Bank = 'Bank';
    case School = 'School';
    case BackToSchool = 'BackToSchool';

    case EndOfLessons = 'EndOfLessons';

    case Optional = 'Optional';

    public function transform(): HolidayType
    {
        return match ($this) {
            self::Public => HolidayType::Public,
            self::Bank => HolidayType::Bank,
            self::School, self::BackToSchool, self::EndOfLessons => HolidayType::School,
            self::Optional => HolidayType::Optional,
        };
    }
}
