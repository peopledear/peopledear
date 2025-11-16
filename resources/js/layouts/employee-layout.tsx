import EmployeeHeaderLayout from "@/layouts/employee/employee-header-layout";
import { type BreadcrumbItem } from "@/types";
import { type ReactNode } from "react";

interface AdminLayoutProps {
    children: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}

export default ({ children, breadcrumbs, ...props }: AdminLayoutProps) => (
    <EmployeeHeaderLayout breadcrumbs={breadcrumbs} {...props}>
        {children}
    </EmployeeHeaderLayout>
);
