import type { HTMLAttributes } from "react";

import { cn } from "@/lib/utils";

type AppLogoProps = HTMLAttributes<HTMLDivElement> & {
    variant?: "internal" | "external";
};

export default function AppLogo({
    variant = "internal",
    className,
    ...props
}: AppLogoProps) {
    if (variant === "external") {
        return (
            <div
                className={cn(
                    "flex items-center gap-4 text-[#1c1c1a] dark:text-white",
                    className,
                )}
                {...props}
            >
                <div className="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-[#1c1c1a] text-2xl font-black tracking-tight text-white shadow-[0_20px_50px_rgba(26,26,26,0.25)] dark:bg-white dark:text-[#18140f]">
                    PD
                </div>
                <div className="flex flex-col leading-none">
                    <span className="text-2xl font-semibold tracking-tight">
                        PeopleDear
                    </span>
                    <span className="text-xs font-semibold tracking-[0.5em] text-[#7c7972] uppercase dark:text-[#d8d4cb]">
                        People operations
                    </span>
                </div>
            </div>
        );
    }

    return (
        <div
            className={cn("flex items-center", className)}
            aria-label="PeopleDear"
            {...props}
        >
            <div className="flex aspect-square size-8 items-center justify-center rounded-md bg-[#f3efe9] text-[#1c1c1a] ring-1 ring-[#e2ddd4] dark:bg-white/10 dark:text-white dark:ring-white/20">
                <span className="text-sm font-black">PD</span>
            </div>
        </div>
    );
}
