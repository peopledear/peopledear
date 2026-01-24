import { AppHeader } from "@/components/app-header";
import { type BreadcrumbItem, type NavItem, TenantedSharedData } from "@/types";
import EmployeeOverviewController from "@/wayfinder/actions/App/Http/Controllers/EmployeeOverviewController";
import EmployeeTimeOffController from "@/wayfinder/actions/App/Http/Controllers/EmployeeTimeOffController";
import { usePage } from "@inertiajs/react";
import { CalendarDays, LayoutGrid } from "lucide-react";

interface AdminHeaderProps {
    breadcrumbs?: BreadcrumbItem[];
}

export function EmployeeHeader({ breadcrumbs = [] }: AdminHeaderProps) {
    const page = usePage<TenantedSharedData>();

    const { props } = page;

    const mainNavItems: NavItem[] = [
        {
            title: "Overview",
            href: EmployeeOverviewController.index(props.tenant.identifier),
            icon: LayoutGrid,
            isActive: page.url.startsWith("/overview"),
        },
        {
            title: "Time Offs",
            href: EmployeeTimeOffController.index(props.tenant.identifier),
            icon: CalendarDays,
            isActive: page.url.startsWith("/time-offs"),
        },
    ];

    return (
        <>
            <AppHeader mainNavItems={mainNavItems} breadcrumbs={breadcrumbs} />
        </>
    );
}
