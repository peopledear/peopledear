import AppLayout from "@/layouts/app-layout";
import { Head, Link } from "@inertiajs/react";
import { ArrowLeftIcon } from "lucide-react";
import OrganizationTimeOffTypesController
    from "@/wayfinder/actions/App/Http/Controllers/OrganizationTimeOffTypesController";

export default function CreateTimeOffTypePage() {
    return (
        <AppLayout>
            <Head title="Create a new Time Off Type" />

            <div className="flex flex-col space-y-6 p-4 sm:p-0">

                <div className="mb-10 flex cursor-pointer items-center space-x-1 text-sm">
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
                    Define a new Time Off Type for your organization.
                </p>
            </div>

            <div className="flex flex-col space-y-6">
                <div className="w-full rounded-md bg-white p-5 text-sm ring-1 ring-gray-200">
                    Here you can create a new Time Off Type for your organization.
                </div>
            </div>
            </div>


        </AppLayout>
    );
}
