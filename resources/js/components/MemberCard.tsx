import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { router, usePage } from "@inertiajs/react";
import { CheckCircle, MoreHorizontal, User, XCircle } from "lucide-react";
import { RoleBadge } from "./RoleBadge";

interface Role {
    id: number;
    name: string;
    display_name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    avatar?: {
        src: string;
    };
    role?: Role;
    is_active: boolean;
}

interface MemberCardProps {
    user: User;
    roles: Role[];
}

export function MemberCard({ user, roles }: MemberCardProps) {
    const { auth } = usePage().props as { auth: { user: any } };
    const isCurrentUser = auth.user?.id === user.id;

    const changeRole = (roleId: number) => {
        router.patch(
            `/admin/users/${user.id}/role`,
            { role_id: roleId },
            {
                preserveScroll: true,
                onSuccess: () => {
                    // TODO: Add toast notification
                    console.log("User role updated successfully");
                },
            },
        );
    };

    const toggleUserStatus = () => {
        const url = user.is_active
            ? `/admin/users/${user.id}/deactivate`
            : `/admin/users/${user.id}/activate`;

        router.post(
            url,
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    // TODO: Add toast notification
                    console.log(
                        `User ${user.is_active ? "deactivated" : "activated"} successfully`,
                    );
                },
            },
        );
    };

    return (
        <Card>
            <CardContent className="flex items-center justify-between gap-4 py-4">
                <div className="flex items-center gap-4">
                    <Avatar className="h-10 w-10">
                        <AvatarImage src={user.avatar?.src} alt={user.name} />
                        <AvatarFallback>
                            {user.name.charAt(0).toUpperCase()}
                        </AvatarFallback>
                    </Avatar>
                    <div>
                        <div className="font-medium">
                            {user.name}
                            {isCurrentUser && (
                                <span className="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                                    (You)
                                </span>
                            )}
                        </div>
                        <div className="text-sm text-gray-500 dark:text-gray-400">
                            {user.email}
                        </div>
                    </div>
                </div>

                <div className="flex items-center gap-3">
                    {user.role && <RoleBadge role={user.role} />}

                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button
                                variant="ghost"
                                size="sm"
                                data-test="user-menu"
                            >
                                <MoreHorizontal className="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuSub>
                                <DropdownMenuSubTrigger>
                                    <User className="mr-2 h-4 w-4" />
                                    Change Role
                                </DropdownMenuSubTrigger>
                                <DropdownMenuSubContent>
                                    {roles.map((role) => (
                                        <DropdownMenuItem
                                            key={role.id}
                                            onClick={() => changeRole(role.id)}
                                            disabled={user.role?.id === role.id}
                                        >
                                            {role.display_name}
                                        </DropdownMenuItem>
                                    ))}
                                </DropdownMenuSubContent>
                            </DropdownMenuSub>

                            {!isCurrentUser && (
                                <>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem
                                        onClick={toggleUserStatus}
                                    >
                                        {user.is_active ? (
                                            <>
                                                <XCircle className="mr-2 h-4 w-4" />
                                                Deactivate
                                            </>
                                        ) : (
                                            <>
                                                <CheckCircle className="mr-2 h-4 w-4" />
                                                Activate
                                            </>
                                        )}
                                    </DropdownMenuItem>
                                </>
                            )}
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </CardContent>
        </Card>
    );
}
