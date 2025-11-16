import { AppHeader } from "@/components/app-header";
import { type BreadcrumbItem, type NavItem } from "@/types";
import { dashboard } from "@/wayfinder/routes";
import { LayoutGrid } from "lucide-react";

interface AdminHeaderProps {
    breadcrumbs?: BreadcrumbItem[];
}

export function EmployeeHeader({ breadcrumbs = [] }: AdminHeaderProps) {
    const mainNavItems: NavItem[] = [
        {
            title: "Overview",
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    return (
        <>
            <AppHeader mainNavItems={mainNavItems} breadcrumbs={breadcrumbs} />
        </>
    );
}
