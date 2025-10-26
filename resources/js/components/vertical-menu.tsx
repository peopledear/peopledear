import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
} from "@/components/ui/navigation-menu";
import { cn } from "@/lib/utils";
import { Link } from "@inertiajs/react";
import { LucideIcon } from "lucide-react";

export interface NavItem {
    label: string;
    href: string;
    icon?: LucideIcon;
    active?: boolean;
}

interface VerticalMenuProps {
    items: NavItem[];
    className?: string;
}

export function VerticalMenu({ items, className }: VerticalMenuProps) {
    return (
        <NavigationMenu
            orientation="vertical"
            className={cn(
                "w-full max-w-none justify-start [&>div]:w-full",
                className,
            )}
        >
            <NavigationMenuList className="flex flex-col items-start space-x-0">
                {items.map((item) => (
                    <NavigationMenuItem key={item.href} className="w-full">
                        <NavigationMenuLink asChild>
                            <Link
                                href={item.href}
                                className={cn(
                                    "group text-muted-foreground hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground bg-background flex h-10 w-full items-start justify-start rounded-md px-4 py-2 text-sm font-medium transition-colors focus:outline-none disabled:pointer-events-none disabled:opacity-50",
                                    item.active &&
                                        "bg-accent text-accent-foreground",
                                )}
                            >
                                {item.icon && (
                                    <item.icon className="mr-2 h-4 w-4" />
                                )}
                                {item.label}
                            </Link>
                        </NavigationMenuLink>
                    </NavigationMenuItem>
                ))}
            </NavigationMenuList>
        </NavigationMenu>
    );
}
