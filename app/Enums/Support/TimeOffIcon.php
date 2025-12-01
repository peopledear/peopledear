<?php

declare(strict_types=1);

namespace App\Enums\Support;

enum TimeOffIcon: string
{
    // Travel & Vacation
    case Plane = 'Plane';
    case PlaneTakeoff = 'PlaneTakeoff';
    case Ship = 'Ship';
    case Car = 'Car';
    case TreePalm = 'TreePalm';
    case Umbrella = 'Umbrella';
    case Sun = 'Sun';
    case Mountain = 'Mountain';
    case Tent = 'Tent';
    case Map = 'Map';

    // Medical & Health
    case Heart = 'Heart';
    case HeartPulse = 'HeartPulse';
    case Hospital = 'Hospital';
    case Stethoscope = 'Stethoscope';
    case Pill = 'Pill';
    case Thermometer = 'Thermometer';
    case Activity = 'Activity';
    case Cross = 'Cross';

    // Family & Personal
    case Baby = 'Baby';
    case Home = 'Home';
    case House = 'House';
    case Sofa = 'Sofa';
    case Bed = 'Bed';
    case Users = 'Users';
    case UserPlus = 'UserPlus';
    case HandHeart = 'HandHeart';

    // Calendar & Time
    case Calendar = 'Calendar';
    case CalendarDays = 'CalendarDays';
    case CalendarCheck = 'CalendarCheck';
    case CalendarClock = 'CalendarClock';
    case Clock = 'Clock';
    case Timer = 'Timer';
    case Hourglass = 'Hourglass';

    // Education & Training
    case GraduationCap = 'GraduationCap';
    case Book = 'Book';
    case BookOpen = 'BookOpen';
    case Notebook = 'Notebook';
    case School = 'School';

    // Work & Business
    case Briefcase = 'Briefcase';
    case Building = 'Building';
    case Gavel = 'Gavel';
    case Scale = 'Scale';
    case FileText = 'FileText';

    // Celebration & Events
    case PartyPopper = 'PartyPopper';
    case Gift = 'Gift';
    case Cake = 'Cake';
    case Church = 'Church';
    case Sparkles = 'Sparkles';

    // Mourning & Bereavement
    case EyeOff = 'EyeOff';
    case CloudRain = 'CloudRain';
    case Flower2 = 'Flower2';

    // Misc
    case Coffee = 'Coffee';
    case Utensils = 'Utensils';
    case Dumbbell = 'Dumbbell';
    case HeartHandshake = 'HeartHandshake';
    case Leaf = 'Leaf';

    /**
     * Get all icons grouped by category for the icon picker.
     *
     * @return array<string, array<string, string>>
     */
    public static function grouped(): array
    {
        return [
            'Travel & Vacation' => [
                self::Plane->value => 'Plane',
                self::PlaneTakeoff->value => 'Plane Takeoff',
                self::Ship->value => 'Ship',
                self::Car->value => 'Car',
                self::TreePalm->value => 'Palm Tree',
                self::Umbrella->value => 'Umbrella',
                self::Sun->value => 'Sun',
                self::Mountain->value => 'Mountain',
                self::Tent->value => 'Tent',
                self::Map->value => 'Map',
            ],
            'Medical & Health' => [
                self::Heart->value => 'Heart',
                self::HeartPulse->value => 'Heart Pulse',
                self::Hospital->value => 'Hospital',
                self::Stethoscope->value => 'Stethoscope',
                self::Pill->value => 'Pill',
                self::Thermometer->value => 'Thermometer',
                self::Activity->value => 'Activity',
                self::Cross->value => 'Cross',
            ],
            'Family & Personal' => [
                self::Baby->value => 'Baby',
                self::Home->value => 'Home',
                self::House->value => 'House',
                self::Sofa->value => 'Sofa',
                self::Bed->value => 'Bed',
                self::Users->value => 'Users',
                self::UserPlus->value => 'User Plus',
                self::HandHeart->value => 'Hand Heart',
            ],
            'Calendar & Time' => [
                self::Calendar->value => 'Calendar',
                self::CalendarDays->value => 'Calendar Days',
                self::CalendarCheck->value => 'Calendar Check',
                self::CalendarClock->value => 'Calendar Clock',
                self::Clock->value => 'Clock',
                self::Timer->value => 'Timer',
                self::Hourglass->value => 'Hourglass',
            ],
            'Education & Training' => [
                self::GraduationCap->value => 'Graduation Cap',
                self::Book->value => 'Book',
                self::BookOpen->value => 'Book Open',
                self::Notebook->value => 'Notebook',
                self::School->value => 'School',
            ],
            'Work & Business' => [
                self::Briefcase->value => 'Briefcase',
                self::Building->value => 'Building',
                self::Gavel->value => 'Gavel',
                self::Scale->value => 'Scale',
                self::FileText->value => 'File Text',
            ],
            'Celebration & Events' => [
                self::PartyPopper->value => 'Party Popper',
                self::Gift->value => 'Gift',
                self::Cake->value => 'Cake',
                self::Church->value => 'Church',
                self::Sparkles->value => 'Sparkles',
            ],
            'Mourning & Bereavement' => [
                self::EyeOff->value => 'Eye Off',
                self::CloudRain->value => 'Cloud Rain',
                self::Flower2->value => 'Flower',
            ],
            'Other' => [
                self::Coffee->value => 'Coffee',
                self::Utensils->value => 'Utensils',
                self::Dumbbell->value => 'Dumbbell',
                self::HeartHandshake->value => 'Heart Handshake',
                self::Leaf->value => 'Leaf',
            ],
        ];
    }

    /**
     * Get all icons as a flat array for validation.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
