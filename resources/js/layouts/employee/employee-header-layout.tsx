import { AppContent } from "@/components/app-content";
import { AppShell } from "@/components/app-shell";
import { EmployeeHeader } from "@/components/employee-header";
import { type BreadcrumbItem } from "@/types";
import type { PropsWithChildren } from "react";

export default function EmployeeHeaderLayout({
    children,
    breadcrumbs,
}: PropsWithChildren<{ breadcrumbs?: BreadcrumbItem[] }>) {
    return (
        <AppShell>
            <EmployeeHeader breadcrumbs={breadcrumbs} />
            <AppContent>{children}</AppContent>
        </AppShell>
    );
}
