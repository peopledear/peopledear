import { TimeOffRequest } from "@/types";
import TimeOffIcon from "./icon";

interface TimeOffTypeDisplayProps {
    type: TimeOffRequest["type"];
    subtitle?: string;
}

export default function TimeOffTypeDisplay({
    type,
    subtitle,
}: TimeOffTypeDisplayProps) {
    return (
        <div className="flex items-start gap-2">
            <TimeOffIcon timeOffType={type} />

            <div className="flex flex-col">
                <span className="font-medium">{type.name}</span>
                {subtitle && (
                    <span className="text-muted-foreground text-sm">
                        {subtitle}
                    </span>
                )}
            </div>
        </div>
    );
}
