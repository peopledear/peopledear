import { Button } from "@/components/ui/button";
import { DropdownMenuItem } from "@/components/ui/dropdown-menu";
import {
    Item,
    ItemActions,
    ItemContent,
    ItemDescription,
    ItemGroup,
    ItemTitle,
} from "@/components/ui/item";
import { Separator } from "@/components/ui/separator";
import { Notification } from "@/types/notifications";
import { EyeOffIcon } from "lucide-react";
import { Fragment } from "react";

interface NotificationsDropdownProps {
    notifications: Notification[];
}

export default function NotificationsDropdown({
    notifications,
}: NotificationsDropdownProps) {
    return (
        <>
            <ItemGroup>
                {notifications.map((notification) => (
                    <Fragment key={notification.id}>
                        <Item>
                            <ItemContent>
                                <ItemTitle>{notification.data.title}</ItemTitle>
                                <ItemDescription>
                                    <span className="line-clamp-4 overflow-hidden text-sm">
                                        {notification.data.message}
                                    </span>
                                </ItemDescription>
                            </ItemContent>
                            <ItemActions>
                                <Button variant="ghost" size="icon">
                                    <EyeOffIcon />
                                </Button>
                            </ItemActions>
                        </Item>
                        <Separator
                            className="bg-border -mx-1 my-1 h-px"
                            data-orientation="custom"
                        />
                    </Fragment>
                ))}
            </ItemGroup>
            <DropdownMenuItem>
                <Button variant="ghost" className="w-full">
                    View All Notifications
                </Button>
            </DropdownMenuItem>
        </>
    );
}
