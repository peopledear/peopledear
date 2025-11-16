import { AppHeader } from "@/components/app-header";
import { overview } from "@/routes/org";
import { type BreadcrumbItem, type NavItem } from "@/types";
import { LayoutGrid, UsersIcon } from "lucide-react";

const mainNavItems: NavItem[] = [
    {
        title: "Overview",
        href: overview(),
        icon: LayoutGrid,
    },
    {
        title: "Manager Portal",
        href: overview(),
        icon: UsersIcon,
    },
];

interface AdminHeaderProps {
    breadcrumbs?: BreadcrumbItem[];
}

export function EmployeeHeader({ breadcrumbs = [] }: AdminHeaderProps) {
    return (
        <>
            <AppHeader mainNavItems={mainNavItems} breadcrumbs={breadcrumbs} />
        </>
    );
}
