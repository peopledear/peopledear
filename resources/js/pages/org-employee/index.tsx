import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { useInitials } from "@/hooks/use-initials";
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
    const getInitials = useInitials();

    return (
        <OrgLayout>
            <Head title="Organization Employees" />
            <div className="flex w-full max-w-6xl flex-col">
                <div>
                    <div className="mb-4 flex items-center justify-between">
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

                <div className="divide-y divide-gray-200 rounded-xl bg-white ring-1 ring-gray-200">
                    {employees?.map((employee) => (
                        <div className="flex items-center justify-between px-6 py-4 text-sm">
                            <div className="flex space-x-2">
                                <Avatar className="size-10 overflow-hidden rounded-full">
                                    <AvatarFallback className="bg-neutral-200 text-xs font-bold text-black dark:bg-neutral-700 dark:text-white">
                                        {getInitials(employee.name)}
                                    </AvatarFallback>
                                </Avatar>
                                <div className="flex flex-col justify-between">
                                    <span className="font-medium">
                                        {employee.name}
                                    </span>
                                    <span className="text-muted-foreground text-sm">
                                        {employee.email}
                                    </span>
                                </div>
                            </div>
                            <div>Actions</div>
                        </div>
                    ))}
                </div>
            </div>
        </OrgLayout>
    );
}
