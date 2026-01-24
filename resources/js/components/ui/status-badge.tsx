import type { VariantProps } from "class-variance-authority";
import type { LucideIcon } from "lucide-react";
import * as React from "react";

import { Badge, badgeVariants } from "@/components/ui/badge";
import { cn } from "@/lib/utils";

type BadgeColor = NonNullable<VariantProps<typeof badgeVariants>["variant"]>;

interface StatusBadgeProps extends Omit<React.ComponentProps<"span">, "color"> {
    label: string;
    color: BadgeColor;
    icon?: LucideIcon;
}

function StatusBadge({
    label,
    color,
    icon: Icon,
    className,
    ...props
}: StatusBadgeProps) {
    return (
        <Badge variant={color} className={cn(className)} {...props}>
            {Icon && <Icon />}
            {label}
        </Badge>
    );
}

export { StatusBadge, type BadgeColor };
