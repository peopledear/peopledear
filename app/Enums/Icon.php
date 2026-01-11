<?php

declare(strict_types=1);

namespace App\Enums;

use App\Data\IconData;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use function collect;
use function implode;

enum Icon: string
{
    // Travel & Vacation
    case LucidePlane = 'LucidePlane';

    case LucidePlaneTakeoff = 'LucidePlaneTakeoff';
    case LucideShip = 'LucideShip';
    case LucideCar = 'LucideCar';
    case LucideTreePalm = 'LucideTreePalm';
    case LucideUmbrella = 'LucideUmbrella';
    case LucideSun = 'LucideSun';
    case LucideMountain = 'LucideMountain';
    case LucideTent = 'LucideTent';
    case LucideMap = 'LucideMap';
    case LucideGlobe = 'LucideGlobe';

    // Medical & Health
    case LucideHeart = 'LucideHeart';
    case LucideHeartPulse = 'LucideHeartPulse';
    case LucideHospital = 'LucideHospital';
    case LucideStethoscope = 'LucideStethoscope';
    case LucidePill = 'LucidePill';
    case LucideThermometer = 'LucideThermometer';
    case LucideActivity = 'LucideActivity';
    case LucideCross = 'LucideCross';

    // Family & Personal
    case LucideBaby = 'LucideBaby';
    case LucideHome = 'LucideHome';
    case LucideHouse = 'LucideHouse';
    case LucideSofa = 'LucideSofa';
    case LucideBed = 'LucideBed';
    case LucideUsers = 'LucideUsers';
    case LucideUserPlus = 'LucideUserPlus';
    case LucideHandHeart = 'LucideHandHeart';

    // Calendar & Time
    case LucideCalendar = 'LucideCalendar';
    case LucideCalendarDays = 'LucideCalendarDays';
    case LucideCalendarCheck = 'LucideCalendarCheck';
    case LucideCalendarClock = 'LucideCalendarClock';
    case LucideClock = 'LucideClock';
    case LucideTimer = 'LucideTimer';
    case LucideHourglass = 'LucideHourglass';

    // Education & Training
    case LucideGraduationCap = 'LucideGraduationCap';
    case LucideBook = 'LucideBook';
    case LucideBookOpen = 'LucideBookOpen';
    case LucideNotebook = 'LucideNotebook';
    case LucideSchool = 'LucideSchool';

    // Work & Business
    case LucideBriefcase = 'LucideBriefcase';
    case LucideBuilding = 'LucideBuilding';
    case LucideGavel = 'LucideGavel';
    case LucideScale = 'LucideScale';
    case LucideFileText = 'LucideFileText';

    // Celebration & Events
    case LucidePartyPopper = 'LucidePartyPopper';
    case LucideGift = 'LucideGift';
    case LucideCake = 'LucideCake';
    case LucideChurch = 'LucideChurch';
    case LucideSparkles = 'LucideSparkles';

    // Mourning & Bereavement
    case LucideEyeOff = 'LucideEyeOff';
    case LucideCloudRain = 'LucideCloudRain';
    case LucideFlower = 'LucideFlower';

    // Misc
    case LucideCoffee = 'LucideCoffee';
    case LucideUtensils = 'LucideUtensils';
    case LucideDumbbell = 'LucideDumbbell';
    case LucideHeartHandshake = 'LucideHeartHandshake';
    case LucideLeaf = 'LucideLeaf';

    /**
     * @return Collection<int, IconData>
     */
    public static function options(): Collection
    {
        /** @var Collection<int, IconData> $ */
        return collect(self::cases())
            ->sortBy(fn (Icon $icon): string => $icon->value)
            ->map(fn (Icon $icon): IconData => IconData::from([
                'value' => $icon->value,
                'name' => $icon->name,
                'icon' => $icon->icon(),
                'label' => $icon->label(),
            ]))->flatten();
    }

    public function icon(): string
    {
        return Str::replace('lucide-', '', Str::kebab($this->name));
    }

    public function label(): string
    {
        return implode(' ', Str::ucsplit($this->name));
    }
}
