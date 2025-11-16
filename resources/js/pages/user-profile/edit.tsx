import { type BreadcrumbItem, type SharedData } from "@/types";
import UserProfileController from "@/wayfinder/actions/App/Http/Controllers/UserProfileController";
import { send } from "@/wayfinder/routes/verification";
import { Transition } from "@headlessui/react";
import { Form, Head, Link, usePage } from "@inertiajs/react";

import DeleteUser from "@/components/delete-user";
import InputError from "@/components/input-error";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import {
    Field,
    FieldContent,
    FieldDescription,
    FieldGroup,
    FieldLabel,
    FieldSeparator,
    FieldSet,
} from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import AppLayout from "@/layouts/app-layout";
import UserSettingsLayout from "@/layouts/settings/app-layout";
import userProfile from "@/wayfinder/routes/user-profile";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Profile settings",
        href: userProfile.edit().url,
    },
];

export default function Edit({ status }: { status?: string }) {
    const { auth } = usePage<SharedData>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Profile settings" />

            <UserSettingsLayout>
                <Card>
                    <CardHeader>
                        <CardTitle>Profile Information</CardTitle>
                        <CardDescription>
                            Update your account's profile information and email
                            address.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            {...UserProfileController.update()}
                            options={{
                                preserveScroll: true,
                            }}
                            className="space-y-4"
                        >
                            {({ processing, recentlySuccessful, errors }) => (
                                <>
                                    <FieldSet>
                                        <FieldSeparator className="-mx-6" />
                                        <FieldGroup>
                                            <Field orientation="responsive">
                                                <FieldContent>
                                                    <FieldLabel htmlFor="name">
                                                        Name
                                                    </FieldLabel>
                                                    <FieldDescription>
                                                        Your full name.
                                                    </FieldDescription>
                                                </FieldContent>
                                                <div className="flex flex-col gap-2">
                                                    <Input
                                                        id="name"
                                                        className="sm:min-w-[300px]"
                                                        defaultValue={
                                                            auth.user.name
                                                        }
                                                        name="name"
                                                        required
                                                        autoComplete="name"
                                                        placeholder="Full name"
                                                    />
                                                    <InputError
                                                        message={errors.name}
                                                    />
                                                </div>
                                            </Field>

                                            <Field orientation="responsive">
                                                <FieldContent>
                                                    <FieldLabel htmlFor="email">
                                                        Email address
                                                    </FieldLabel>
                                                    <FieldDescription>
                                                        The email address used
                                                        for authentication and
                                                        notifications.
                                                    </FieldDescription>
                                                </FieldContent>
                                                <div className="flex flex-col gap-2">
                                                    <Input
                                                        id="email"
                                                        type="email"
                                                        className="sm:min-w-[300px]"
                                                        defaultValue={
                                                            auth.user.email
                                                        }
                                                        name="email"
                                                        required
                                                        autoComplete="username"
                                                        placeholder="Email address"
                                                    />
                                                    <InputError
                                                        message={errors.email}
                                                    />
                                                </div>
                                            </Field>
                                        </FieldGroup>
                                        <FieldSeparator className="-mx-6" />
                                    </FieldSet>

                                    {auth.user.email_verified_at === null && (
                                        <div>
                                            <p className="text-muted-foreground -mt-4 text-sm">
                                                Your email address is
                                                unverified.{" "}
                                                <Link
                                                    href={send()}
                                                    as="button"
                                                    className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                                >
                                                    Click here to resend the
                                                    verification email.
                                                </Link>
                                            </p>

                                            {status ===
                                                "verification-link-sent" && (
                                                <div className="mt-2 text-sm font-medium text-green-600">
                                                    A new verification link has
                                                    been sent to your email
                                                    address.
                                                </div>
                                            )}
                                        </div>
                                    )}

                                    <div className="flex items-center gap-4">
                                        <Button
                                            disabled={processing}
                                            data-test="update-profile-button"
                                        >
                                            Save
                                        </Button>

                                        <Transition
                                            show={recentlySuccessful}
                                            enter="transition ease-in-out"
                                            enterFrom="opacity-0"
                                            leave="transition ease-in-out"
                                            leaveTo="opacity-0"
                                        >
                                            <p className="text-sm text-neutral-600">
                                                Saved
                                            </p>
                                        </Transition>
                                    </div>
                                </>
                            )}
                        </Form>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle className="text-destructive">
                            Danger Zone
                        </CardTitle>
                        <CardDescription>
                            Permanently delete your account.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <DeleteUser />
                    </CardContent>
                </Card>
            </UserSettingsLayout>
        </AppLayout>
    );
}
