import { AppContent } from "@/components/app-content";
import { AppShell } from "@/components/app-shell";
import { EmployeeHeader } from "@/components/employee-header";
import { type BreadcrumbItem } from "@/types";
import type { PropsWithChildren, ReactNode } from "react";

export default function EmployeeHeaderLayout({
    children,
    pageHeader,
    breadcrumbs,
}: PropsWithChildren<{
    pageHeader?: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}>) {
    return (
        <AppShell>
            <EmployeeHeader breadcrumbs={breadcrumbs} />
            <AppContent pageHeader={pageHeader}>{children}</AppContent>
        </AppShell>
    );
}
