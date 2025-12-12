import TimeOffIcon from "@/components/time-offs/icon";
import { Badge } from "@/components/ui/badge";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import AdminLayout from "@/layouts/org-layout";
import OrgSettingsLayout from "@/layouts/settings/org-layout";
import { cn } from "@/lib/utils";
import { TimeOffType } from "@/types";
import { Head } from "@inertiajs/react";
import { Circle, CircleCheck } from "lucide-react";

interface TimeOffTypesPageProps {
    timeOffTypes: TimeOffType[];
}

export default function TimeOffTypesPage({
    timeOffTypes,
}: TimeOffTypesPageProps) {
    console.log(timeOffTypes);

    return (
        <AdminLayout>
            <Head title="Time Off Types" />
            <OrgSettingsLayout>
                <Card>
                    <CardHeader>
                        <CardTitle>Time Off Types</CardTitle>
                        <CardDescription>
                            Manage Time Off Types of your organization, edit
                            system types and bring your own organization time
                            off types.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {timeOffTypes.map((timeOffType: TimeOffType) => (
                            <div
                                key={timeOffType.id}
                                className="flex items-center space-x-6 py-4"
                            >
                                <div>
                                    <TimeOffIcon timeOffType={timeOffType} />
                                </div>
                                <div className="flex flex-col">
                                    <span className="font-semibold">
                                        {timeOffType.name}
                                    </span>
                                    <span className="text-muted-foreground text-sm">
                                        {timeOffType.description}
                                    </span>
                                </div>
                                <div>
                                    <Badge
                                        variant={
                                            timeOffType.isSystem
                                                ? "default"
                                                : "destructive"
                                        }
                                        className={cn(
                                            timeOffType.isSystem
                                                ? "bg-green-500 dark:bg-green-600"
                                                : "bg-red-500 dark:bg-red-600",
                                            "text-white",
                                        )}
                                    >
                                        {timeOffType.isSystem ? (
                                            <CircleCheck className="size-4" />
                                        ) : (
                                            <Circle className="size-4" />
                                        )}
                                        System
                                    </Badge>
                                </div>

                                <div>
                                    <Badge
                                        variant={
                                            timeOffType.isActive
                                                ? "default"
                                                : "destructive"
                                        }
                                        className={cn(
                                            timeOffType.isActive
                                                ? "bg-green-500 dark:bg-green-600"
                                                : "bg-red-500 dark:bg-red-600",
                                            "text-white",
                                        )}
                                    >
                                        {timeOffType.isSystem ? (
                                            <CircleCheck className="size-4" />
                                        ) : (
                                            <Circle className="size-4" />
                                        )}
                                        Active
                                    </Badge>
                                </div>
                            </div>
                        ))}
                    </CardContent>
                </Card>
            </OrgSettingsLayout>
        </AdminLayout>
    );
}
