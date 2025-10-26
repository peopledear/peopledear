import OrgHeaderLayout from "@/layouts/org/org-header-layout";
import { type BreadcrumbItem } from "@/types";
import { type ReactNode } from "react";

interface AdminLayoutProps {
    children: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}

export default ({ children, breadcrumbs, ...props }: AdminLayoutProps) => (
    <OrgHeaderLayout breadcrumbs={breadcrumbs} {...props}>
        {children}
    </OrgHeaderLayout>
);
