import { RoleBadge } from "@/components/RoleBadge";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { AuthLayout } from "@/layouts/AuthLayout";
import { useForm } from "@inertiajs/react";
import { Eye, EyeOff } from "lucide-react";
import { useState } from "react";

interface AcceptInvitationPageProps {
    invitation: {
        email: string;
        role: string;
        token: string;
    };
}

export default function AcceptInvitation({
    invitation,
}: AcceptInvitationPageProps) {
    const [showPassword, setShowPassword] = useState(false);
    const [showPasswordConfirmation, setShowPasswordConfirmation] =
        useState(false);

    const { data, setData, post, processing, errors } = useForm({
        name: "",
        password: "",
        password_confirmation: "",
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(`/invitation/${invitation.token}`, {
            preserveScroll: true,
        });
    };

    return (
        <AuthLayout
            header={
                <div>
                    <div className="text-xl font-semibold">
                        Accept Your Invitation
                    </div>
                    <div className="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        You've been invited to join PeopleDear
                    </div>
                </div>
            }
        >
            <div className="mb-6 flex flex-col gap-2 rounded-lg bg-gray-100 p-4 dark:bg-gray-800">
                <div className="flex items-center justify-between">
                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email
                    </span>
                    <span className="text-sm text-gray-600 dark:text-gray-400">
                        {invitation.email}
                    </span>
                </div>
                <div className="flex items-center justify-between">
                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Role
                    </span>
                    <RoleBadge role={invitation.role} />
                </div>
            </div>

            <form onSubmit={submit} className="flex flex-col gap-y-6">
                <div className="space-y-2">
                    <Label htmlFor="name">Full Name</Label>
                    <Input
                        id="name"
                        name="name"
                        value={data.name}
                        onChange={(e) => setData("name", e.target.value)}
                        placeholder="Enter your full name"
                        className="w-full"
                        autoComplete="name"
                    />
                    {errors.name && (
                        <p className="text-sm text-red-600">{errors.name}</p>
                    )}
                </div>

                <div className="space-y-2">
                    <Label htmlFor="password">Password</Label>
                    <div className="relative">
                        <Input
                            id="password"
                            name="password"
                            type={showPassword ? "text" : "password"}
                            value={data.password}
                            onChange={(e) =>
                                setData("password", e.target.value)
                            }
                            placeholder="Create a password"
                            className="w-full pr-10"
                            autoComplete="new-password"
                        />
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            className="absolute top-0 right-0 h-full px-3 py-2 hover:bg-transparent"
                            onClick={() => setShowPassword(!showPassword)}
                            aria-label={
                                showPassword ? "Hide password" : "Show password"
                            }
                        >
                            {showPassword ? (
                                <EyeOff className="h-4 w-4" />
                            ) : (
                                <Eye className="h-4 w-4" />
                            )}
                        </Button>
                    </div>
                    {errors.password && (
                        <p className="text-sm text-red-600">
                            {errors.password}
                        </p>
                    )}
                </div>

                <div className="space-y-2">
                    <Label htmlFor="password_confirmation">
                        Confirm Password
                    </Label>
                    <div className="relative">
                        <Input
                            id="password_confirmation"
                            name="password_confirmation"
                            type={
                                showPasswordConfirmation ? "text" : "password"
                            }
                            value={data.password_confirmation}
                            onChange={(e) =>
                                setData("password_confirmation", e.target.value)
                            }
                            placeholder="Confirm your password"
                            className="w-full pr-10"
                            autoComplete="new-password"
                        />
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            className="absolute top-0 right-0 h-full px-3 py-2 hover:bg-transparent"
                            onClick={() =>
                                setShowPasswordConfirmation(
                                    !showPasswordConfirmation,
                                )
                            }
                            aria-label={
                                showPasswordConfirmation
                                    ? "Hide password"
                                    : "Show password"
                            }
                        >
                            {showPasswordConfirmation ? (
                                <EyeOff className="h-4 w-4" />
                            ) : (
                                <Eye className="h-4 w-4" />
                            )}
                        </Button>
                    </div>
                    {errors.password_confirmation && (
                        <p className="text-sm text-red-600">
                            {errors.password_confirmation}
                        </p>
                    )}
                </div>

                <Button type="submit" className="w-full" disabled={processing}>
                    {processing ? "Creating Account..." : "Create Account"}
                </Button>
            </form>
        </AuthLayout>
    );
}
