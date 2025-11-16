import { AppHeader } from "@/components/app-header";
import { type BreadcrumbItem, type NavItem } from "@/types";
import { dashboard } from "@/wayfinder/routes";
import { overview } from "@/wayfinder/routes/org";
import { edit } from "@/wayfinder/routes/org/settings/organization";
import {
    Calendar,
    CheckSquare,
    Clock,
    LayoutGrid,
    Settings,
    UsersIcon,
} from "lucide-react";

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
        href: dashboard(),
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

interface AdminHeaderProps {
    breadcrumbs?: BreadcrumbItem[];
}

export function OrgHeader({ breadcrumbs = [] }: AdminHeaderProps) {
    return (
        <>
            <AppHeader mainNavItems={mainNavItems} breadcrumbs={breadcrumbs} />
        </>
    );
}
