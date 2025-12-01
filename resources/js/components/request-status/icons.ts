/**
 * Curated icons for request status display.
 * This map mirrors App\Enums\Support\RequestStatusIcon on the backend.
 * Only these icons are bundled to keep the bundle size small.
 */
import type { LucideIcon } from "lucide-react";
import {
    Activity,
    AlertCircle,
    AlertTriangle,
    BadgeCheck,
    Ban,
    // Approved / Success
    Check,
    CheckCircle,
    CheckCircle2,
    CircleCheck,
    CircleDashed,
    CircleDot,
    CircleMinus,
    CircleOff,
    CircleX,
    Clock,
    Clock4,

    // Review / Needs Attention
    Eye,
    FastForward,
    FileSearch,
    Hand,
    HelpCircle,
    Hourglass,
    Info,
    Loader,
    MessageCircle,
    MinusCircle,
    Pause,
    // On Hold / Paused
    PauseCircle,
    Play,
    // In Progress / Processing
    RefreshCw,
    RotateCw,
    Search,
    ShieldCheck,
    ShieldX,
    // Cancelled / Stopped
    Slash,
    Square,
    StopCircle,
    ThumbsDown,
    ThumbsUp,
    // Pending / Waiting
    Timer,
    Trash2,
    Undo2,
    // Rejected / Error
    X,
    XCircle,
    Zap,
} from "lucide-react";

export const requestStatusIcons = {
    // Pending / Waiting
    Timer,
    Clock,
    Hourglass,
    Loader,
    CircleDashed,
    CircleDot,
    Pause,
    AlertCircle,

    // Approved / Success
    Check,
    CheckCircle,
    CheckCircle2,
    CircleCheck,
    ThumbsUp,
    BadgeCheck,
    ShieldCheck,

    // Rejected / Error
    X,
    XCircle,
    CircleX,
    CircleOff,
    ThumbsDown,
    Ban,
    ShieldX,
    AlertTriangle,

    // Cancelled / Stopped
    Slash,
    MinusCircle,
    CircleMinus,
    Square,
    StopCircle,
    Trash2,
    Undo2,

    // In Progress / Processing
    RefreshCw,
    RotateCw,
    Activity,
    Zap,
    Play,
    FastForward,

    // On Hold / Paused
    PauseCircle,
    Hand,
    Clock4,

    // Review / Needs Attention
    Eye,
    Search,
    FileSearch,
    HelpCircle,
    MessageCircle,
    Info,
} as const;

export type RequestStatusIconName = keyof typeof requestStatusIcons;

export function getRequestStatusIcon(name: string): LucideIcon | null {
    return (
        (requestStatusIcons[name as RequestStatusIconName] as LucideIcon) ??
        null
    );
}
