import AdminHeaderLayout from "@/layouts/admin/admin-header-layout";
import { type BreadcrumbItem } from "@/types";
import { type ReactNode } from "react";

interface AdminLayoutProps {
    children: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}

export default ({ children, breadcrumbs, ...props }: AdminLayoutProps) => (
    <AdminHeaderLayout breadcrumbs={breadcrumbs} {...props}>
        {children}
    </AdminHeaderLayout>
);
