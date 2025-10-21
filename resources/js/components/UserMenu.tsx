import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { store } from "@/wayfinder/actions/App/Http/Controllers/Auth/LogoutController";
import { index } from "@/wayfinder/actions/App/Http/Controllers/Profile/UserProfileController";
import { router, usePage } from "@inertiajs/react";
import { ChevronsUpDown, LogOut, User } from "lucide-react";

interface UserMenuProps {
    collapsed?: boolean;
}

export function UserMenu({ collapsed = false }: UserMenuProps) {
    const { auth } = usePage().props as { auth: { user: any } };

    const logout = () => {
        router.visit(store().url, {
            method: "post",
        });
    };

    const goToProfile = () => {
        router.visit(index().url, {
            preserveScroll: true,
        });
    };

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button
                    variant="ghost"
                    className={`w-full justify-start ${collapsed ? "px-2" : "px-3"}`}
                >
                    <Avatar className="h-8 w-8">
                        <AvatarImage src={auth.user?.avatar} />
                        <AvatarFallback>
                            {auth.user?.name?.charAt(0)?.toUpperCase()}
                        </AvatarFallback>
                    </Avatar>
                    {!collapsed && (
                        <>
                            <span className="ml-2 flex-1 text-left">
                                {auth.user?.name}
                            </span>
                            <ChevronsUpDown className="h-4 w-4 opacity-50" />
                        </>
                    )}
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="center" className="w-56">
                <DropdownMenuLabel>
                    <div className="flex flex-col space-y-1">
                        <p className="text-sm leading-none font-medium">
                            {auth.user?.name}
                        </p>
                        <p className="text-muted-foreground text-xs">
                            {auth.user?.email}
                        </p>
                    </div>
                </DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuItem onClick={goToProfile}>
                    <User className="mr-2 h-4 w-4" />
                    <span>Account</span>
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem onClick={logout}>
                    <LogOut className="mr-2 h-4 w-4" />
                    <span>Logout</span>
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
