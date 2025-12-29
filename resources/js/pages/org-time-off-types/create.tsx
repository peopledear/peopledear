import AppLayout from "@/layouts/app-layout";
import TimeOffTypeForm from "@/pages/org-time-off-types/form";
import { BalanceType, Icon, TimeOffUnit } from "@/types";
import OrganizationTimeOffTypesController from "@/wayfinder/actions/App/Http/Controllers/OrganizationTimeOffTypesController";
import { Head, Link } from "@inertiajs/react";
import { ArrowLeftIcon } from "lucide-react";

interface CreateTimeOffTypePageProps {
    balanceTypes: BalanceType[];
    timeOffUnits: TimeOffUnit[];
    icons: Icon[];
}

export default function CreateTimeOffTypePage({
    balanceTypes,
    timeOffUnits,
    icons,
}: CreateTimeOffTypePageProps) {
    return (
        <AppLayout>
            <Head title="Create a new Time Off Type" />

            <div className="mx-auto flex w-full max-w-xl flex-col space-y-6 p-4 sm:p-0">
                <div className="mb-6 flex cursor-pointer items-center space-x-1 text-sm">
                    <Link
                        href={OrganizationTimeOffTypesController.index()}
                        className="flex items-center space-x-1 text-sm text-gray-500 hover:text-gray-700"
                    >
                        <ArrowLeftIcon size="16" />
                        <span>Back</span>
                    </Link>
                </div>
                <div className="flex flex-col space-y-2">
                    <h2 className="font-semibold">Create Time Off Type</h2>
                    <p className="text-muted-foreground text-sm">
                        Define a new time off type for your organization.
                    </p>
                </div>

                <div className="flex flex-col space-y-6">
                    <TimeOffTypeForm
                        balanceTypes={balanceTypes}
                        timeOffUnits={timeOffUnits}
                        icons={icons}
                    />
                </div>
            </div>
        </AppLayout>
    );
}
