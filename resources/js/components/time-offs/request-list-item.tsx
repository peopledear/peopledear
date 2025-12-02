import RequestStatusBadge from "@/components/request-status/badge";
import { TimeOffRequest } from "@/types";
import TimeOffTypeDisplay from "./type-display";
import { formatDateRange } from "./utils";

interface TimeOffRequestListItemProps {
    request: TimeOffRequest;
}

export default function TimeOffRequestListItem({
    request,
}: TimeOffRequestListItemProps) {
    return (
        <div className="flex items-center justify-between px-6 py-4 text-sm">
            <div className="lg:w-[150px]">
                <TimeOffTypeDisplay
                    type={request.type}
                    subtitle={formatDateRange(
                        request.startDate,
                        request.endDate,
                    )}
                />
            </div>

            <RequestStatusBadge status={request.status} className="w-[100px]" />
            <div className="flex flex-col items-start">
                <span className="font-medium">Period</span>
                <span className="text-muted-foreground text-sm">
                    {request.period.year}
                </span>
            </div>
        </div>
    );
}
