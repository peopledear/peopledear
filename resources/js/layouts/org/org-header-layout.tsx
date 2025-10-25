import { AppContent } from "@/components/app-content";
import { AppShell } from "@/components/app-shell";
import { OrgHeader } from "@/components/org-header";
import { type BreadcrumbItem } from "@/types";
import type { PropsWithChildren } from "react";

export default function OrgHeaderLayout({
    children,
    breadcrumbs,
}: PropsWithChildren<{ breadcrumbs?: BreadcrumbItem[] }>) {
    return (
        <AppShell>
            <OrgHeader breadcrumbs={breadcrumbs} />
            <AppContent>{children}</AppContent>
        </AppShell>
    );
}
