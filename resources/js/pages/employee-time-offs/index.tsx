import RequestStatusBadge from "@/components/request-status/badge";
import TimeOffTypeDisplay from "@/components/time-offs/type-display";
import { formatDateRange } from "@/components/time-offs/utils";
import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationLink,
    PaginationNext,
    PaginationPrevious,
} from "@/components/ui/pagination";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import EmployeeLayout from "@/layouts/employee-layout";
import {
    BreadcrumbItem,
    EnumOptions,
    PaginatedResponse,
    TimeOffRequest,
} from "@/types";
import EmployeeTimeOffController from "@/wayfinder/actions/App/Http/Controllers/EmployeeTimeOffController";
import { Head, router } from "@inertiajs/react";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Time Offs",
        href: EmployeeTimeOffController.index().url,
    },
];

interface TimeOffsPageProps {
    timeOffRequests: PaginatedResponse<TimeOffRequest>;
    types: EnumOptions;
    statuses: EnumOptions;
    filters: {
        status: number | null;
        type: number | null;
    };
}

export default function TimeOffsIndex({
    timeOffRequests,
    types,
    statuses,
    filters,
}: TimeOffsPageProps) {
    const handleStatusChange = (value: string) => {
        router.get(
            EmployeeTimeOffController.index().url,
            {
                status: value === "all" ? undefined : value,
                type: filters.type ?? undefined,
            },
            { preserveState: true },
        );
    };

    const handleTypeChange = (value: string) => {
        router.get(
            EmployeeTimeOffController.index().url,
            {
                status: filters.status ?? undefined,
                type: value === "all" ? undefined : value,
            },
            { preserveState: true },
        );
    };

    const handlePageChange = (url: string | null) => {
        if (url) {
            router.get(url, {}, { preserveState: true });
        }
    };

    return (
        <EmployeeLayout
            breadcrumbs={breadcrumbs}
            pageHeader={
                <div className="rounded-t-xl border-b border-gray-200 px-4 py-10 sm:px-6">
                    <div className="mx-auto flex w-full max-w-6xl flex-col">
                        <div className="flex flex-col space-y-6 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                            <div className="flex items-center space-x-4">
                                <div>
                                    <h1 className="text-xl font-semibold">
                                        Time Offs
                                    </h1>
                                    <span className="text-muted-foreground text-sm">
                                        View and manage your time off requests
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            }
        >
            <Head title="Time Offs" />

            <div className="flex w-full max-w-6xl flex-col space-y-6">
                <div className="flex items-center gap-4">
                    <Select
                        value={filters.status?.toString() ?? "all"}
                        onValueChange={handleStatusChange}
                    >
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Filter by status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Statuses</SelectItem>
                            {Object.entries(statuses).map(([value, label]) => (
                                <SelectItem key={value} value={value}>
                                    {label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>

                    <Select
                        value={filters.type?.toString() ?? "all"}
                        onValueChange={handleTypeChange}
                    >
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Filter by type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Types</SelectItem>
                            {Object.entries(types).map(([value, label]) => (
                                <SelectItem key={value} value={value}>
                                    {label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                <div className="rounded-xl bg-white ring-1 ring-gray-200">
                    {timeOffRequests.data.length === 0 ? (
                        <div className="text-muted-foreground p-6 text-center text-sm">
                            No time off requests found.
                        </div>
                    ) : (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Details</TableHead>
                                    <TableHead>Status</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {timeOffRequests.data.map((request) => (
                                    <TableRow key={request.id}>
                                        <TableCell>
                                            <TimeOffTypeDisplay
                                                type={request.type}
                                                subtitle={formatDateRange(
                                                    request.startDate,
                                                    request.endDate,
                                                )}
                                            />
                                        </TableCell>
                                        <TableCell>
                                            <RequestStatusBadge
                                                status={request.status}
                                            />
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}
                </div>

                {timeOffRequests.last_page > 1 && (
                    <Pagination>
                        <PaginationContent>
                            <PaginationItem>
                                <PaginationPrevious
                                    onClick={() =>
                                        handlePageChange(
                                            timeOffRequests.prev_page_url,
                                        )
                                    }
                                    className={
                                        !timeOffRequests.prev_page_url
                                            ? "pointer-events-none opacity-50"
                                            : "cursor-pointer"
                                    }
                                />
                            </PaginationItem>
                            {timeOffRequests.links
                                .slice(1, -1)
                                .map((link, index) => (
                                    <PaginationItem key={index}>
                                        <PaginationLink
                                            onClick={() =>
                                                handlePageChange(link.url)
                                            }
                                            isActive={link.active}
                                            className="cursor-pointer"
                                        >
                                            {link.label}
                                        </PaginationLink>
                                    </PaginationItem>
                                ))}
                            <PaginationItem>
                                <PaginationNext
                                    onClick={() =>
                                        handlePageChange(
                                            timeOffRequests.next_page_url,
                                        )
                                    }
                                    className={
                                        !timeOffRequests.next_page_url
                                            ? "pointer-events-none opacity-50"
                                            : "cursor-pointer"
                                    }
                                />
                            </PaginationItem>
                        </PaginationContent>
                    </Pagination>
                )}
            </div>
        </EmployeeLayout>
    );
}
