import { Button } from "@/components/ui/button";
import {
    DropdownMenu,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import useComponent from "@/hooks/use-component";
import { NotificationList } from "@/types/notifications";
import { BellIcon } from "lucide-react";

interface NotificationsDropdownProps {
    href: string;
}

interface NotificationResponseProps {
    data: NotificationList | null;
}

export default function NotificationsDropdown({
    href,
}: NotificationsDropdownProps) {
    const { component, props } = useComponent<NotificationResponseProps>(
        href,
        15000,
    );

    const Loaded = component?.default;

    return (
        <div className="inline">
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button variant="ghost" size="icon" className="relative">
                        <BellIcon className="size-5" />
                        {props?.data?.unread !== undefined &&
                            props?.data?.unread > 0 && (
                                <span className="absolute top-1 right-1.5 inline-flex h-3 w-3 items-center justify-center rounded-full border-2 border-white bg-green-500 text-xs text-white" />
                            )}
                    </Button>
                </DropdownMenuTrigger>
                {Loaded ? <Loaded {...(props?.data ?? {})} /> : null}
            </DropdownMenu>
        </div>
    );
}
