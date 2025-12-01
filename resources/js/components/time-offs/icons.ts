/**
 * Curated icons for time-off types.
 * This map mirrors App\Enums\Support\TimeOffIcon on the backend.
 * Only these icons are bundled to keep the bundle size small (~5-10KB).
 */
import type { LucideIcon } from "lucide-react";
import {
    Activity,
    // Family & Personal
    Baby,
    Bed,
    Book,
    BookOpen,
    // Work & Business
    Briefcase,
    Building,
    Cake,
    // Calendar & Time
    Calendar,
    CalendarCheck,
    CalendarClock,
    CalendarDays,
    Car,
    Church,
    Clock,
    CloudRain,
    // Other
    Coffee,
    Cross,
    Dumbbell,
    // Mourning & Bereavement
    EyeOff,
    FileText,
    Flower2,
    Gavel,
    Gift,
    // Education & Training
    GraduationCap,
    HandHeart,
    // Medical & Health
    Heart,
    HeartHandshake,
    HeartPulse,
    Home,
    Hospital,
    Hourglass,
    House,
    Leaf,
    Map,
    Mountain,
    Notebook,
    Palmtree,
    // Celebration & Events
    PartyPopper,
    Pill,
    // Travel & Vacation
    Plane,
    PlaneTakeoff,
    Scale,
    School,
    Ship,
    Sofa,
    Sparkles,
    Stethoscope,
    Sun,
    Tent,
    Thermometer,
    Timer,
    Umbrella,
    UserPlus,
    Users,
    Utensils,
} from "lucide-react";

export const timeOffIcons = {
    // Travel & Vacation
    Plane,
    PlaneTakeoff,
    Ship,
    Car,
    Palmtree,
    Umbrella,
    Sun,
    Mountain,
    Tent,
    Map,

    // Medical & Health
    Heart,
    HeartPulse,
    Hospital,
    Stethoscope,
    Pill,
    Thermometer,
    Activity,
    Cross,

    // Family & Personal
    Baby,
    Home,
    House,
    Sofa,
    Bed,
    Users,
    UserPlus,
    HandHeart,

    // Calendar & Time
    Calendar,
    CalendarDays,
    CalendarCheck,
    CalendarClock,
    Clock,
    Timer,
    Hourglass,

    // Education & Training
    GraduationCap,
    Book,
    BookOpen,
    Notebook,
    School,

    // Work & Business
    Briefcase,
    Building,
    Gavel,
    Scale,
    FileText,

    // Celebration & Events
    PartyPopper,
    Gift,
    Cake,
    Church,
    Sparkles,

    // Mourning & Bereavement
    EyeOff,
    CloudRain,
    Flower2,

    // Other
    Coffee,
    Utensils,
    Dumbbell,
    HeartHandshake,
    Leaf,
} as const;

export type TimeOffIconName = keyof typeof timeOffIcons;

export function getTimeOffIcon(name: string): LucideIcon | null {
    return (timeOffIcons[name as TimeOffIconName] as LucideIcon) ?? null;
}
