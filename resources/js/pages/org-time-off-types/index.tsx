import Icon from "@/components/time-offs/icon";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import {
    Item,
    ItemActions,
    ItemContent,
    ItemDescription,
    ItemGroup,
    ItemMedia,
    ItemSeparator,
    ItemTitle,
} from "@/components/ui/item";
import AdminLayout from "@/layouts/org-layout";
import OrgSettingsLayout from "@/layouts/settings/org-layout";
import { cn } from "@/lib/utils";
import { TimeOffType } from "@/types";
import OrganizationTimeOffTypesController from "@/wayfinder/actions/App/Http/Controllers/OrganizationTimeOffTypesController";
import { Head, Link } from "@inertiajs/react";
import { Circle, CircleCheck, Ellipsis, PlusIcon } from "lucide-react";
import { Fragment } from "react";

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
                <Card className="pb-0.5">
                    <CardHeader>
                        <CardTitle>Time Off Types</CardTitle>
                        <CardDescription>
                            <div className="flex flex-col items-start justify-between space-y-4 md:flex-row md:space-x-6">
                                <div>
                                    Manage Time Off Types of your organization,
                                    edit system types and bring your own
                                    organization time off types.
                                </div>
                                <div>
                                    <Link
                                        href={OrganizationTimeOffTypesController.create()}
                                    >
                                        <Button
                                            variant="outline"
                                            className="md:-mt-2"
                                        >
                                            <PlusIcon className="size-4" />
                                            Create Time Off Type
                                        </Button>
                                    </Link>
                                </div>
                            </div>
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="mx-0.5 rounded-lg border px-0">
                        <ItemGroup>
                            {timeOffTypes.map(
                                (timeOffType: TimeOffType, index: number) => (
                                    <Fragment key={timeOffType.id}>
                                        <Item key={timeOffType.id}>
                                            <ItemMedia>
                                                <Icon
                                                    name={timeOffType.icon.icon}
                                                />
                                            </ItemMedia>
                                            <ItemContent className="gap-1">
                                                <ItemTitle>
                                                    {timeOffType.name}
                                                </ItemTitle>
                                                <ItemDescription>
                                                    {timeOffType.description}
                                                </ItemDescription>
                                            </ItemContent>
                                            <div>
                                                {timeOffType.isSystem && (
                                                    <Badge
                                                        variant={
                                                            timeOffType.isSystem
                                                                ? "outline"
                                                                : "destructive"
                                                        }
                                                        className={cn(
                                                            timeOffType.isSystem
                                                                ? "border-emerald-500 bg-emerald-500 text-emerald-500"
                                                                : "border-red-500 bg-red-500 text-red-500",
                                                            "rounded-md px-3 py-0.5 text-xs font-semibold dark:bg-transparent",
                                                        )}
                                                    >
                                                        {timeOffType.isSystem ? (
                                                            <CircleCheck className="size-4" />
                                                        ) : (
                                                            <Circle className="size-4" />
                                                        )}
                                                        System
                                                    </Badge>
                                                )}
                                            </div>

                                            <div>
                                                {timeOffType.isActive && (
                                                    <Badge
                                                        variant={
                                                            timeOffType.isActive
                                                                ? "outline"
                                                                : "destructive"
                                                        }
                                                        className={cn(
                                                            timeOffType.isActive
                                                                ? "border-emerald-500 bg-emerald-500 text-emerald-500"
                                                                : "border-red-500 bg-red-500 text-red-500",
                                                            "rounded-md px-3 py-0.5 text-xs font-semibold dark:bg-transparent",
                                                        )}
                                                    >
                                                        <CircleCheck className="size-4" />
                                                        Active
                                                    </Badge>
                                                )}
                                            </div>
                                            <ItemActions>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    className="size-8 rounded-md"
                                                >
                                                    <Ellipsis className="size-4" />
                                                </Button>
                                            </ItemActions>
                                        </Item>
                                        {index !== timeOffTypes.length - 1 && (
                                            <ItemSeparator />
                                        )}
                                    </Fragment>
                                ),
                            )}
                        </ItemGroup>
                    </CardContent>
                </Card>
            </OrgSettingsLayout>
        </AdminLayout>
    );
}
