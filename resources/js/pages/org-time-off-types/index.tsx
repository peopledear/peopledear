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
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import AdminLayout from "@/layouts/org-layout";
import OrgSettingsLayout from "@/layouts/settings/org-layout";
import { cn } from "@/lib/utils";
import { TenantedSharedData, TimeOffType, TimeOffTypeStatus } from "@/types";
import { create } from "@/wayfinder/routes/tenant/settings/time-off-types";
import { Head, Link, usePage } from "@inertiajs/react";
import {
    Circle,
    CircleCheck,
    Ellipsis,
    PlusIcon,
    ShieldCheck,
} from "lucide-react";
import { Fragment } from "react";

interface TimeOffTypesPageProps {
    timeOffTypes: TimeOffType[];
}

export default function TimeOffTypesPage({
    timeOffTypes,
}: TimeOffTypesPageProps) {
    const { props } = usePage<TenantedSharedData>();

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
                                        href={create(props.tenant.identifier)}
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
                                                    <Tooltip>
                                                        <TooltipTrigger>
                                                            <ShieldCheck className="size-4 text-teal-500" />
                                                        </TooltipTrigger>
                                                        <TooltipContent>
                                                            System time off
                                                            type.
                                                        </TooltipContent>
                                                    </Tooltip>
                                                )}
                                            </div>

                                            <div>
                                                <Badge
                                                    variant="outline"
                                                    className={cn(
                                                        timeOffType.status ===
                                                            TimeOffTypeStatus.Active
                                                            ? "border-emerald-500 bg-emerald-500 text-emerald-500"
                                                            : timeOffType.status ===
                                                                TimeOffTypeStatus.Pending
                                                              ? "border-yellow-500 bg-yellow-500 text-yellow-500"
                                                              : "border-gray-500 bg-gray-500 text-gray-500",
                                                        "rounded-md px-3 py-0.5 text-xs font-semibold dark:bg-transparent",
                                                    )}
                                                >
                                                    {timeOffType.status ===
                                                    TimeOffTypeStatus.Active ? (
                                                        <CircleCheck className="size-4" />
                                                    ) : (
                                                        <Circle className="size-4" />
                                                    )}
                                                    {timeOffType.status ===
                                                    TimeOffTypeStatus.Active
                                                        ? "Active"
                                                        : timeOffType.status ===
                                                            TimeOffTypeStatus.Pending
                                                          ? "Pending"
                                                          : "Inactive"}
                                                </Badge>
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
