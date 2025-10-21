import { MemberCard } from "@/components/MemberCard";
import { RoleBadge } from "@/components/RoleBadge";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Separator } from "@/components/ui/separator";
import { SettingsLayout } from "@/layouts/SettingsLayout";
import { router, useForm } from "@inertiajs/react";

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

interface Invitation {
    id: number;
    email: string;
    role: Role;
    inviter: {
        name: string;
    };
    expires_at: string;
}

interface MembersPageProps {
    users: {
        data: User[];
    };
    pendingInvitations: Invitation[];
    roles: Role[];
}

export default function Members({
    users,
    pendingInvitations,
    roles,
}: MembersPageProps) {
    const { data, setData, post, processing, errors } = useForm({
        email: "",
        role_id: null as number | null,
    });

    const submitInvitation = (e: React.FormEvent) => {
        e.preventDefault();
        post("/admin/invitations", {
            preserveScroll: true,
            onSuccess: () => {
                setData("email", "");
                setData("role_id", null);
                console.log("Invitation sent successfully");
            },
        });
    };

    const resendInvitation = (invitationId: number) => {
        router.post(
            `/admin/invitations/${invitationId}/resend`,
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    console.log("Invitation resent successfully");
                },
            },
        );
    };

    const revokeInvitation = (invitationId: number) => {
        router.delete(`/admin/invitations/${invitationId}`, {
            preserveScroll: true,
            onSuccess: () => {
                console.log("Invitation revoked successfully");
            },
        });
    };

    return (
        <SettingsLayout>
            <form id="invite-form" onSubmit={submitInvitation}>
                <Card className="mb-4">
                    <CardHeader>
                        <CardTitle>Invite by email</CardTitle>
                        <CardDescription>
                            Add new members to your organization by sending them
                            an invitation email.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Button
                            id="send-invite"
                            form="invite-form"
                            type="submit"
                            className="w-fit lg:ms-auto"
                            disabled={processing}
                        >
                            {processing ? "Sending..." : "Send invite"}
                        </Button>
                    </CardContent>
                </Card>

                <Card className="mb-8">
                    <CardContent className="space-y-6 pt-6">
                        <div className="flex items-start justify-between gap-4 max-sm:flex-col">
                            <div className="space-y-1">
                                <Label htmlFor="email">Email</Label>
                                <p className="text-sm text-gray-600">
                                    The email address of the person you want to
                                    invite.
                                </p>
                            </div>
                            <Input
                                id="email"
                                name="email"
                                type="email"
                                value={data.email}
                                onChange={(e) =>
                                    setData("email", e.target.value)
                                }
                                placeholder="colleague@example.com"
                                className="w-full lg:w-80"
                            />
                        </div>
                        {errors.email && (
                            <p className="text-sm text-red-600">
                                {errors.email}
                            </p>
                        )}

                        <Separator />

                        <div className="flex items-start justify-between gap-4 max-sm:flex-col">
                            <div className="space-y-1">
                                <Label htmlFor="role_id">Role</Label>
                                <p className="text-sm text-gray-600">
                                    The role this person will have in your
                                    organization.
                                </p>
                            </div>
                            <Select
                                value={data.role_id?.toString()}
                                onValueChange={(value) =>
                                    setData("role_id", parseInt(value))
                                }
                            >
                                <SelectTrigger
                                    id="role-select"
                                    className="w-full lg:w-80"
                                >
                                    <SelectValue placeholder="Select a role" />
                                </SelectTrigger>
                                <SelectContent>
                                    {roles.map((role) => (
                                        <SelectItem
                                            key={role.id}
                                            value={role.id.toString()}
                                            data-test={`role-${role.id}`}
                                        >
                                            {role.display_name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>
                        {errors.role_id && (
                            <p className="text-sm text-red-600">
                                {errors.role_id}
                            </p>
                        )}
                    </CardContent>
                </Card>
            </form>

            <Card className="mb-4">
                <CardHeader>
                    <CardTitle>Pending invitations</CardTitle>
                    <CardDescription>
                        Invitations that have been sent but not yet accepted.
                    </CardDescription>
                </CardHeader>
            </Card>

            {pendingInvitations.length > 0 ? (
                <Card className="mb-8">
                    <CardContent className="pt-6">
                        {pendingInvitations.map((invitation, index) => (
                            <div key={invitation.id}>
                                <div className="flex items-center justify-between gap-4 py-4">
                                    <div className="flex items-center gap-4">
                                        <Avatar className="h-10 w-10">
                                            <AvatarImage
                                                alt={invitation.email}
                                            />
                                            <AvatarFallback>
                                                {invitation.email
                                                    .charAt(0)
                                                    .toUpperCase()}
                                            </AvatarFallback>
                                        </Avatar>
                                        <div>
                                            <div className="font-medium">
                                                {invitation.email}
                                            </div>
                                            <div className="text-sm text-gray-500 dark:text-gray-400">
                                                Invited by{" "}
                                                {invitation.inviter.name}
                                            </div>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-3">
                                        <RoleBadge role={invitation.role} />
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() =>
                                                resendInvitation(invitation.id)
                                            }
                                        >
                                            Resend
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() =>
                                                revokeInvitation(invitation.id)
                                            }
                                        >
                                            Revoke
                                        </Button>
                                    </div>
                                </div>
                                {index < pendingInvitations.length - 1 && (
                                    <Separator />
                                )}
                            </div>
                        ))}
                    </CardContent>
                </Card>
            ) : (
                <Card className="mb-8">
                    <CardContent className="pt-6">
                        <div className="text-center text-gray-500">
                            <p className="font-medium">
                                No pending invitations
                            </p>
                            <p className="text-sm">
                                There are no pending invitations at the moment.
                            </p>
                        </div>
                    </CardContent>
                </Card>
            )}

            <Card className="mb-4">
                <CardHeader>
                    <CardTitle>Organization members</CardTitle>
                    <CardDescription>
                        People who are currently members of your organization.
                    </CardDescription>
                </CardHeader>
            </Card>

            <Card>
                <CardContent className="pt-6">
                    {users.data.map((user, index) => (
                        <div key={user.id}>
                            <MemberCard user={user} roles={roles} />
                            {index < users.data.length - 1 && (
                                <Separator className="my-4" />
                            )}
                        </div>
                    ))}
                </CardContent>
            </Card>
        </SettingsLayout>
    );
}
