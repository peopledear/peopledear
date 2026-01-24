<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Curated icons for request status display.
 * These are the only icons available for request statuses to keep bundle size small.
 */
enum RequestStatusIcon: string
{
    // Pending / Waiting
    case Timer = 'Timer';
    case Clock = 'Clock';
    case Hourglass = 'Hourglass';
    case Loader = 'Loader';
    case CircleDashed = 'CircleDashed';
    case CircleDot = 'CircleDot';
    case Pause = 'Pause';
    case AlertCircle = 'AlertCircle';

    // Approved / Success
    case Check = 'Check';
    case CheckCircle = 'CheckCircle';
    case CheckCircle2 = 'CheckCircle2';
    case CircleCheck = 'CircleCheck';
    case ThumbsUp = 'ThumbsUp';
    case BadgeCheck = 'BadgeCheck';
    case ShieldCheck = 'ShieldCheck';

    // Rejected / Error
    case X = 'X';
    case XCircle = 'XCircle';
    case CircleX = 'CircleX';
    case CircleOff = 'CircleOff';
    case ThumbsDown = 'ThumbsDown';
    case Ban = 'Ban';
    case ShieldX = 'ShieldX';
    case AlertTriangle = 'AlertTriangle';

    // Cancelled / Stopped
    case Slash = 'Slash';
    case MinusCircle = 'MinusCircle';
    case CircleMinus = 'CircleMinus';
    case Square = 'Square';
    case StopCircle = 'StopCircle';
    case Trash2 = 'Trash2';
    case Undo2 = 'Undo2';

    // In Progress / Processing
    case RefreshCw = 'RefreshCw';
    case RotateCw = 'RotateCw';
    case Activity = 'Activity';
    case Zap = 'Zap';
    case Play = 'Play';
    case FastForward = 'FastForward';

    // On Hold / Paused
    case PauseCircle = 'PauseCircle';
    case Hand = 'Hand';
    case Clock4 = 'Clock4';

    // Review / Needs Attention
    case Eye = 'Eye';
    case Search = 'Search';
    case FileSearch = 'FileSearch';
    case HelpCircle = 'HelpCircle';
    case MessageCircle = 'MessageCircle';
    case Info = 'Info';

    /**
     * Get all icons grouped by category for the icon picker.
     *
     * @return array<string, array<string, string>>
     */
    public static function grouped(): array
    {
        return [
            'Pending / Waiting' => [
                self::Timer->value => 'Timer',
                self::Clock->value => 'Clock',
                self::Hourglass->value => 'Hourglass',
                self::Loader->value => 'Loader',
                self::CircleDashed->value => 'Circle Dashed',
                self::CircleDot->value => 'Circle Dot',
                self::Pause->value => 'Pause',
                self::AlertCircle->value => 'Alert Circle',
            ],
            'Approved / Success' => [
                self::Check->value => 'Check',
                self::CheckCircle->value => 'Check Circle',
                self::CheckCircle2->value => 'Check Circle 2',
                self::CircleCheck->value => 'Circle Check',
                self::ThumbsUp->value => 'Thumbs Up',
                self::BadgeCheck->value => 'Badge Check',
                self::ShieldCheck->value => 'Shield Check',
            ],
            'Rejected / Error' => [
                self::X->value => 'X',
                self::XCircle->value => 'X Circle',
                self::CircleX->value => 'Circle X',
                self::CircleOff->value => 'Circle Off',
                self::ThumbsDown->value => 'Thumbs Down',
                self::Ban->value => 'Ban',
                self::ShieldX->value => 'Shield X',
                self::AlertTriangle->value => 'Alert Triangle',
            ],
            'Cancelled / Stopped' => [
                self::Slash->value => 'Slash',
                self::MinusCircle->value => 'Minus Circle',
                self::CircleMinus->value => 'Circle Minus',
                self::Square->value => 'Square',
                self::StopCircle->value => 'Stop Circle',
                self::Trash2->value => 'Trash',
                self::Undo2->value => 'Undo',
            ],
            'In Progress / Processing' => [
                self::RefreshCw->value => 'Refresh',
                self::RotateCw->value => 'Rotate',
                self::Activity->value => 'Activity',
                self::Zap->value => 'Zap',
                self::Play->value => 'Play',
                self::FastForward->value => 'Fast Forward',
            ],
            'On Hold / Paused' => [
                self::PauseCircle->value => 'Pause Circle',
                self::Hand->value => 'Hand',
                self::Clock4->value => 'Clock',
            ],
            'Review / Needs Attention' => [
                self::Eye->value => 'Eye',
                self::Search->value => 'Search',
                self::FileSearch->value => 'File Search',
                self::HelpCircle->value => 'Help Circle',
                self::MessageCircle->value => 'Message Circle',
                self::Info->value => 'Info',
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
