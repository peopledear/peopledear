import { SidebarInset } from "@/components/ui/sidebar";
import * as React from "react";

interface AppContentProps extends React.ComponentProps<"main"> {
    variant?: "header" | "sidebar";
}

export function AppContent({
    variant = "header",
    children,
    ...props
}: AppContentProps) {
    if (variant === "sidebar") {
        return <SidebarInset {...props}>{children}</SidebarInset>;
    }

    return (
        <main className="flex flex-1 flex-col px-0 py-px sm:px-2" {...props}>
            <div
                id="main-content-wrapper"
                className="relative mx-auto flex w-full grow flex-col items-stretch rounded-none bg-gray-50 shadow-xs ring-0 ring-gray-200 sm:rounded-lg sm:ring-1"
            >
                <div
                    className="mx-auto w-full max-w-[1920px] px-4 pt-4 pb-20 sm:px-6 sm:pt-10"
                    data-slot="main-content"
                >
                    <div className="flex w-full flex-col items-start justify-center gap-x-6 gap-y-4 sm:flex-row">
                        {children}
                    </div>
                </div>
            </div>
        </main>
    );
}
