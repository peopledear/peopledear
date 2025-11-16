import EmployeeHeaderLayout from "@/layouts/employee/employee-header-layout";
import { type BreadcrumbItem } from "@/types";
import { type ReactNode } from "react";

interface AdminLayoutProps {
    children: ReactNode;
    pageHeader?: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}

export default ({
    children,
    pageHeader,
    breadcrumbs,
    ...props
}: AdminLayoutProps) => (
    <EmployeeHeaderLayout
        breadcrumbs={breadcrumbs}
        {...props}
        pageHeader={pageHeader}
    >
        {children}
    </EmployeeHeaderLayout>
);
