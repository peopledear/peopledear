import { Button } from "@/components/ui/button";
import OrgLayout from "@/layouts/org-layout";
import { Head } from "@inertiajs/react";
import { PlusIcon } from "lucide-react";

interface Employee {
    id: number;
    name: string;
    email: string;
    phone?: string;
    job_title?: string;
    hire_date?: string;
    status?: "active" | "inactive" | "on_leave";
}

interface OrgEmployeePageProps {
    employees?: Employee[];
}

export default function OrgEmployeePage({ employees }: OrgEmployeePageProps) {
    return (
        <OrgLayout>
            <Head title="Organization Employees" />
            <div className="flex w-full max-w-6xl flex-col gap-y-8 sm:gap-y-12">
                <div>
                    <div className="mb-2 flex items-center justify-between">
                        <div>
                            <h2 className="font-medium">Employees</h2>
                        </div>
                        <div>
                            <Button>
                                <PlusIcon />
                                New Employee
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </OrgLayout>
    );
}
