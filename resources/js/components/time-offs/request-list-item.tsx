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
            <TimeOffTypeDisplay
                type={request.type}
                subtitle={formatDateRange(request.startDate, request.endDate)}
            />

            <RequestStatusBadge status={request.status} className="w-[100px]" />
        </div>
    );
}
