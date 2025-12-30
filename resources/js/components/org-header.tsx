import { AppHeader } from "@/components/app-header";
import { type BreadcrumbItem, type NavItem, SharedData } from "@/types";
import { dashboard } from "@/wayfinder/routes";
import { overview } from "@/wayfinder/routes/org";
import { index } from "@/wayfinder/routes/org/employees";
import { edit } from "@/wayfinder/routes/org/settings/organization";
import { usePage } from "@inertiajs/react";
import {
    Calendar,
    CheckSquare,
    Clock,
    LayoutGrid,
    Settings,
    UsersIcon,
} from "lucide-react";

interface AdminHeaderProps {
    breadcrumbs?: BreadcrumbItem[];
}

export function OrgHeader({ breadcrumbs = [] }: AdminHeaderProps) {
    const { organization } = usePage<SharedData>().props;

    const mainNavItems: NavItem[] = [
        {
            title: "Overview",
            href: overview(),
            icon: LayoutGrid,
            show: true,
        },
        {
            title: "Approvals",
            href: dashboard(),
            icon: CheckSquare,
            show: true,
        },
        {
            title: "Time Off",
            href: dashboard(),
            icon: Calendar,
            show: true,
        },
        {
            title: "Overtime",
            href: dashboard(),
            icon: Clock,
            show: true,
        },
        {
            title: "Employees",
            href: index(),
            icon: UsersIcon,
            show: true,
        },
        {
            title: "Settings",
            href: edit(organization?.id ?? ""),
            icon: Settings,
            show: true,
        },
    ];

    return (
        <>
            <AppHeader mainNavItems={mainNavItems} breadcrumbs={breadcrumbs} />
        </>
    );
}
