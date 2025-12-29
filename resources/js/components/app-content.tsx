import { SidebarInset } from "@/components/ui/sidebar";
import * as React from "react";

interface AppContentProps extends React.ComponentProps<"main"> {
    variant?: "header" | "sidebar";
    pageHeader?: React.ReactNode;
}

export function AppContent({
    variant = "header",
    children,
    pageHeader,
    ...props
}: AppContentProps) {
    if (variant === "sidebar") {
        return <SidebarInset {...props}>{children}</SidebarInset>;
    }

    return (
        <main
            className="bg-background flex flex-1 flex-col px-0 py-px sm:px-2"
            {...props}
        >
            <div
                id="main-content-wrapper"
                className="bg-weak ring-weak relative mx-auto flex w-full grow flex-col items-stretch rounded-none shadow-xs ring-0 sm:rounded-lg sm:ring-1"
            >
                {pageHeader && pageHeader}
                <div
                    className="mx-auto w-full max-w-480 px-4 pt-4 pb-20 sm:px-6 sm:pt-10"
                    data-slot="main-content"
                >
                    <div className="flex w-full flex-col justify-center gap-x-6 gap-y-4 xl:flex-row xl:items-start">
                        {children}
                    </div>
                </div>
            </div>
        </main>
    );
}
