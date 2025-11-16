import { Button } from "@/components/ui/button";
import EmployeeLayout from "@/layouts/employee-layout";
import { BreadcrumbItem } from "@/types";
import { overview } from "@/wayfinder/routes/employee";
import { Head } from "@inertiajs/react";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Dashboard",
        href: overview().url,
    },
];

export default function EmployeeOverview() {
    return (
        <EmployeeLayout
            breadcrumbs={breadcrumbs}
            pageHeader={
                <div className="rounded-t-xl border-b border-gray-200 bg-gray-50 px-4 py-10 sm:px-6">
                    <div className="mx-auto flex w-full max-w-6xl flex-col">
                        {/* Page Header To be extracted later */}
                        <div className="flex items-center justify-between">
                            <div className="flex items-center space-x-4">
                                <div className="flex h-12 w-12 items-center justify-center overflow-hidden rounded-md border border-green-300 bg-green-100">
                                    FB
                                </div>
                                <div>
                                    <h1 className="text-xl font-semibold">
                                        Francisco Barrento
                                    </h1>
                                    <span className="text-muted-foreground text-sm">
                                        @francisco.barrento
                                    </span>
                                </div>
                            </div>
                            <div>
                                <Button>New Time Off</Button>
                            </div>
                        </div>
                    </div>
                </div>
            }
        >
            <Head title="Employee Overview" />
            <div className="flex w-full max-w-6xl flex-col">
                {/* Main Content To be extracted later */}
                <div className="mt-8">
                    <h2 className="text-lg font-medium">
                        Welcome back, Francisco!
                    </h2>
                    <p className="mt-2 text-sm text-gray-600">
                        Here is an overview of your recent activities and
                        upcoming events.
                    </p>
                </div>
            </div>
        </EmployeeLayout>
    );
}
