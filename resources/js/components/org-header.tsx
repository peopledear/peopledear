import { AppHeader } from "@/components/app-header";
import { type BreadcrumbItem, type NavItem } from "@/types";
import { dashboard } from "@/wayfinder/routes";
import { index } from "@/wayfinder/routes/org/employees";
import { overview } from "@/wayfinder/routes/tenant/org";
import { edit } from "@/wayfinder/routes/tenant/settings/organization";
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
            href: edit(),
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
