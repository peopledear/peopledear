import { cn } from "@/lib/utils";
import { getRequestStatusIcon } from "./icons";

interface RequestStatus {
    status: number;
    label: string;
    icon: string;
    color: string;
}

interface RequestStatusBadgeProps {
    status: RequestStatus;
    className?: string;
}

const colorStyles: Record<string, string> = {
    yellow: "text-yellow-600",
    green: "text-green-600",
    red: "text-red-600",
    gray: "text-gray-500",
};

export default function RequestStatusBadge({
    status,
    className,
}: RequestStatusBadgeProps) {
    const Icon = getRequestStatusIcon(status.icon);
    const colorClass = colorStyles[status.color] ?? "text-muted-foreground";

    return (
        <div className={cn("flex items-center gap-2", className)}>
            {Icon && <Icon className={cn("size-4", colorClass)} />}
            <span>{status.label}</span>
        </div>
    );
}
