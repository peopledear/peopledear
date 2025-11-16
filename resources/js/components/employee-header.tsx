import { AppHeader } from "@/components/app-header";
import { type BreadcrumbItem, type NavItem, type SharedData } from "@/types";
import { dashboard } from "@/wayfinder/routes";
import { overview } from "@/wayfinder/routes/org";
import { usePage } from "@inertiajs/react";
import { EyeIcon, LayoutGrid } from "lucide-react";

interface AdminHeaderProps {
    breadcrumbs?: BreadcrumbItem[];
}

export function EmployeeHeader({ breadcrumbs = [] }: AdminHeaderProps) {
    const page = usePage<SharedData>();
    const { show } = page.props;

    const mainNavItems: NavItem[] = [
        {
            title: "Overview",
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: "Manage",
            href: overview(),
            icon: EyeIcon,
            show: show.orgLink,
        },
    ];

    return (
        <>
            <AppHeader mainNavItems={mainNavItems} breadcrumbs={breadcrumbs} />
        </>
    );
}
