import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from "@/components/ui/dropdown-menu";
import { ToggleGroup, ToggleGroupItem } from "@/components/ui/toggle-group";
import { UserInfo } from "@/components/user-info";
import { type Appearance, useAppearance } from "@/hooks/use-appearance";
import { useMobileNavigation } from "@/hooks/use-mobile-navigation";
import { TenantedSharedData, type User } from "@/types";
import UserProfileController from "@/wayfinder/actions/App/Http/Controllers/UserProfileController";
import { logout } from "@/wayfinder/routes/tenant/auth";
import { Link, router, usePage } from "@inertiajs/react";
import { LogOut, Monitor, Moon, Settings, Sun, SwatchBook } from "lucide-react";

interface UserMenuContentProps {
    user: User;
}

export function UserMenuContent({ user }: UserMenuContentProps) {
    const cleanup = useMobileNavigation();
    const { props } = usePage<TenantedSharedData>();
    const { appearance, updateAppearance } = useAppearance();

    const handleLogout = () => {
        cleanup();
        router.flushAll();
    };

    return (
        <>
            <DropdownMenuLabel className="p-0 font-normal">
                <div className="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                    <UserInfo user={user} showEmail={true} />
                </div>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuGroup>
                <DropdownMenuItem asChild>
                    <Link
                        className="block w-full"
                        href={UserProfileController.edit(
                            props.tenant.identifier,
                        )}
                        as="button"
                        prefetch
                        onClick={cleanup}
                    >
                        <Settings className="mr-2 size-5" />
                        Account
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuGroup>
            <DropdownMenuSeparator />
            <DropdownMenuItem asChild>
                <Link
                    className="block w-full"
                    href={logout(props.tenant.identifier)}
                    as="button"
                    onClick={handleLogout}
                    data-test="logout-button"
                >
                    <LogOut className="mr-2 size-5" />
                    Log out
                </Link>
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem
                className="justify-between hover:bg-transparent focus:bg-transparent"
                onSelect={(e) => e.preventDefault()}
            >
                <span className="flex items-center gap-2">
                    <SwatchBook className="mr-2 size-5" />
                    Theme
                </span>
                <ToggleGroup
                    type="single"
                    value={appearance}
                    onValueChange={(value) => {
                        if (value) {
                            updateAppearance(value as Appearance);
                        }
                    }}
                    variant="outline"
                    size="sm"
                >
                    <ToggleGroupItem value="light" aria-label="Light theme">
                        <Sun className="size-4" />
                    </ToggleGroupItem>
                    <ToggleGroupItem value="dark" aria-label="Dark theme">
                        <Moon className="size-4" />
                    </ToggleGroupItem>
                    <ToggleGroupItem value="system" aria-label="System theme">
                        <Monitor className="size-4" />
                    </ToggleGroupItem>
                </ToggleGroup>
            </DropdownMenuItem>
        </>
    );
}
