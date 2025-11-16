import { AppHeader } from "@/components/app-header";
import { type BreadcrumbItem, type NavItem, SharedData } from "@/types";
import { overview } from "@/wayfinder/routes/employee";
import { usePage } from "@inertiajs/react";
import { LayoutGrid } from "lucide-react";

interface AdminHeaderProps {
    breadcrumbs?: BreadcrumbItem[];
}

export function EmployeeHeader({ breadcrumbs = [] }: AdminHeaderProps) {
    const page = usePage<SharedData>();

    const mainNavItems: NavItem[] = [
        {
            title: "Overview",
            href: overview(),
            icon: LayoutGrid,
            isActive: page.url.startsWith("/overview"),
        },
    ];

    return (
        <>
            <AppHeader mainNavItems={mainNavItems} breadcrumbs={breadcrumbs} />
        </>
    );
}
