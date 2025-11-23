import { Button } from "@/components/ui/button";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import useComponent from "@/hooks/use-component";
import { BellIcon, LucideInbox } from "lucide-react";

interface NotificationsDropdownProps {
    href: string;
}

export default function NotificationsDropdown({
    href,
}: NotificationsDropdownProps) {
    const { component, props } = useComponent(href, 15000);

    const Loaded = component?.default;

    return (
        <div className="inline">
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button variant="ghost" size="icon" className="relative">
                        <BellIcon className="size-5" />
                        <span className="absolute top-1.5 right-2 inline-flex h-2.5 w-2.5 items-center justify-center rounded-full border-2 border-white bg-red-500 text-xs text-white" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    className="max-w-80 min-w-64"
                    align="start"
                >
                    <DropdownMenuLabel>
                        <div className="flex items-center space-x-2">
                            <LucideInbox className="text-muted-foreground size-5" />
                            <span>Notifications</span>
                        </div>
                    </DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    {Loaded ? <Loaded {...(props ?? {})} /> : null}
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    );
}
