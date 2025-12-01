import { cn } from "@/lib/utils";
import { TimeOffType } from "@/types";
import { getTimeOffIcon } from "./icons";

interface TimeOffIconProps {
    timeOffType: TimeOffType;
    size?: string;
}

export default function TimeOffIcon({
    timeOffType,
    size = "size-4",
}: TimeOffIconProps) {
    const Icon = getTimeOffIcon(timeOffType.icon);

    if (!Icon) {
        return null;
    }

    return (
        <div className="flex items-center justify-center rounded-sm bg-black p-2.5 text-white dark:bg-gray-700 dark:text-gray-400">
            <Icon className={cn(size)} />
        </div>
    );
}
