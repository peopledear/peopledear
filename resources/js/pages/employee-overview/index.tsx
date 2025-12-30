import TimeOffRequestListItem from "@/components/time-offs/request-list-item";
import { Button } from "@/components/ui/button";
import EmployeeLayout from "@/layouts/employee-layout";
import { SectionCards } from "@/pages/employee-overview/section-cards";
import {
    BreadcrumbItem,
    Employee,
    EnumOptions,
    TimeOffRequest,
    VacationBalance,
} from "@/types";
import EmployeeOverviewController from "@/wayfinder/actions/App/Http/Controllers/EmployeeOverviewController";
import EmployeeTimeOffController from "@/wayfinder/actions/App/Http/Controllers/EmployeeTimeOffController";
import { Head, Link } from "@inertiajs/react";
import { PlusIcon } from "lucide-react";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Dashboard",
        href: EmployeeOverviewController.index().url,
    },
];

interface EmployeeOverviewPageProps {
    employee: Employee;
    vacationBalance: VacationBalance;
    timeOffRequests: TimeOffRequest[];
    types: EnumOptions;
    statuses: EnumOptions;
}

export default function EmployeeOverview({
    employee,
    vacationBalance,
    timeOffRequests,
}: EmployeeOverviewPageProps) {
    return (
        <EmployeeLayout
            breadcrumbs={breadcrumbs}
            pageHeader={
                <div className="rounded-t-xl border-b border-gray-200 px-4 py-10 sm:px-6">
                    <div className="mx-auto flex w-full max-w-6xl flex-col">
                        <div className="flex flex-col space-y-6 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                            <div className="flex items-center space-x-4">
                                <div className="flex h-12 w-12 items-center justify-center overflow-hidden rounded-md border border-green-300 bg-green-100">
                                    FB
                                </div>
                                <div>
                                    <h1 className="text-xl font-semibold">
                                        {employee.name}
                                    </h1>
                                    <span className="text-muted-foreground text-sm">
                                        @{employee.user?.name}
                                    </span>
                                </div>
                            </div>
                            <div className="flex items-center space-x-4">
                                <Button variant="outline">
                                    <PlusIcon />
                                    Overtime
                                </Button>
                                <Link
                                    href={
                                        EmployeeTimeOffController.create().url
                                    }
                                >
                                    <Button>
                                        <PlusIcon />
                                        Time Off
                                    </Button>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            }
        >
            <Head title="Employee Overview" />

            <div className="flex w-full max-w-6xl flex-col space-y-6 space-x-0 sm:space-x-8">
                <SectionCards vacationBalance={vacationBalance} />

                <div className="flex w-full max-w-6xl flex-col gap-y-8 sm:gap-y-12">
                    <div>
                        <div className="mb-2 flex items-center justify-between">
                            <div>
                                <h2 className="font-medium">
                                    Recent Time Off Requests
                                </h2>
                            </div>
                            <div>
                                <Link
                                    href={
                                        EmployeeTimeOffController.create().url
                                    }
                                >
                                    <Button size="icon" variant="outline">
                                        <PlusIcon />
                                    </Button>
                                </Link>
                            </div>
                        </div>
                        <div className="divide-y divide-gray-200 rounded-xl bg-white ring-1 ring-gray-200">
                            {timeOffRequests.length === 0 && (
                                <div className="text-muted-foreground p-6 text-center text-sm">
                                    No time off requests found.
                                </div>
                            )}
                            {timeOffRequests.map((request) => (
                                <TimeOffRequestListItem
                                    key={request.id}
                                    request={request}
                                />
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </EmployeeLayout>
    );
}
