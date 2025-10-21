import { AvatarSelector } from "@/components/AvatarSelector";
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
import { Separator } from "@/components/ui/separator";
import { ProfileLayout } from "@/layouts/ProfileLayout";
import { destroy } from "@/wayfinder/actions/App/Http/Controllers/Profile/UserAvatarController";
import { update } from "@/wayfinder/actions/App/Http/Controllers/Profile/UserProfileController";
import { router, useForm } from "@inertiajs/react";
import { useState } from "react";

interface GeneralProfilePageProps {
    user: {
        name: string;
        email: string;
        avatar: {
            src: string;
        };
    };
}

export default function General({ user }: GeneralProfilePageProps) {
    const [avatar, setAvatar] = useState<File | null>(null);

    const { data, setData, post, processing, errors } = useForm({
        name: user.name,
        email: user.email,
        avatar: null as File | null,
        _method: "PUT",
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        const formData = new FormData();
        formData.append("name", data.name);
        formData.append("email", data.email);
        formData.append("_method", "PUT");
        if (avatar) {
            formData.append("avatar", avatar);
        }

        post(update().url, {
            data: formData,
            preserveScroll: true,
            onSuccess: () => {
                setAvatar(null);
                console.log("Profile updated successfully");
            },
            onError: (errors) => {
                console.error("Validation errors:", errors);
            },
        });
    };

    const removeAvatar = () => {
        router.delete(destroy(), {
            preserveScroll: true,
            onSuccess: () => {
                console.log("Avatar removed successfully");
            },
        });
    };

    return (
        <ProfileLayout>
            <form id="settings" onSubmit={submit}>
                <Card className="mb-4">
                    <CardHeader>
                        <CardTitle>General</CardTitle>
                        <CardDescription>
                            General account settings related to your profile.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Button
                            form="settings"
                            type="submit"
                            className="w-fit lg:ms-auto"
                            disabled={processing}
                        >
                            {processing ? "Saving..." : "Save changes"}
                        </Button>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent className="space-y-6 pt-6">
                        <div className="flex items-start justify-between gap-4 max-sm:flex-col">
                            <div className="space-y-1">
                                <Label htmlFor="name">Name</Label>
                                <p className="text-sm text-gray-600">
                                    Your full name.
                                </p>
                            </div>
                            <Input
                                id="name"
                                name="name"
                                value={data.name}
                                onChange={(e) =>
                                    setData("name", e.target.value)
                                }
                                className="w-full lg:w-80"
                            />
                        </div>
                        {errors.name && (
                            <p className="text-sm text-red-600">
                                {errors.name}
                            </p>
                        )}

                        <Separator />

                        <div className="flex items-start justify-between gap-4 max-sm:flex-col">
                            <div className="space-y-1">
                                <Label htmlFor="email">Email</Label>
                                <p className="text-sm text-gray-600">
                                    The email address you use for authentication
                                    and notifications.
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
                                <Label>Profile photo</Label>
                                <p className="text-sm text-gray-600">
                                    Used for attribution on time offs requests
                                    and other events.
                                </p>
                            </div>
                            <AvatarSelector
                                modelValue={avatar}
                                currentAvatarUrl={user.avatar.src}
                                label={data.name}
                                error={errors.avatar}
                                onChange={setAvatar}
                                onDelete={removeAvatar}
                            />
                        </div>
                    </CardContent>
                </Card>
            </form>
        </ProfileLayout>
    );
}
