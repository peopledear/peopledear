import InputError from "@/components/input-error";
import AppLayout from "@/layouts/app-layout";
import UserSettingsLayout from "@/layouts/settings/app-layout";
import { type BreadcrumbItem, type TenantedSharedData } from "@/types";
import UserPasswordController from "@/wayfinder/actions/App/Http/Controllers/UserPasswordController";
import { edit } from "@/wayfinder/routes/tenant/user/settings/password";
import { Transition } from "@headlessui/react";
import { Form, Head, usePage } from "@inertiajs/react";
import { useRef } from "react";

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
import { PasswordInput } from "@/components/ui/password-input";

export default function Password() {
    const { props } = usePage<TenantedSharedData>();
    const passwordInput = useRef<HTMLInputElement>(null);
    const currentPasswordInput = useRef<HTMLInputElement>(null);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: "Password settings",
            href: edit(props.tenant.identifier).url,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Password settings" />

            <UserSettingsLayout>
                <Card>
                    <CardHeader>
                        <CardTitle>Update Password</CardTitle>
                        <CardDescription>
                            Ensure your account is using a long, random password
                            to stay secure.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            {...UserPasswordController.update(
                                props.tenant.identifier,
                            )}
                            options={{
                                preserveScroll: true,
                            }}
                            resetOnError={[
                                "password",
                                "password_confirmation",
                                "current_password",
                            ]}
                            resetOnSuccess
                            onError={(errors) => {
                                if (errors.password) {
                                    passwordInput.current?.focus();
                                }

                                if (errors.current_password) {
                                    currentPasswordInput.current?.focus();
                                }
                            }}
                            className="space-y-4"
                        >
                            {({ errors, processing, recentlySuccessful }) => (
                                <>
                                    <FieldSet>
                                        <FieldSeparator className="-mx-6" />
                                        <FieldGroup>
                                            <Field orientation="responsive">
                                                <FieldContent>
                                                    <FieldLabel htmlFor="current_password">
                                                        Current password
                                                    </FieldLabel>
                                                    <FieldDescription>
                                                        Enter your current
                                                        password to confirm your
                                                        identity.
                                                    </FieldDescription>
                                                </FieldContent>
                                                <div className="flex flex-col gap-2">
                                                    <PasswordInput
                                                        id="current_password"
                                                        ref={
                                                            currentPasswordInput
                                                        }
                                                        name="current_password"
                                                        className="sm:min-w-75"
                                                        autoComplete="current-password"
                                                        placeholder="Current password"
                                                    />
                                                    <InputError
                                                        message={
                                                            errors.current_password
                                                        }
                                                    />
                                                </div>
                                            </Field>

                                            <Field orientation="responsive">
                                                <FieldContent>
                                                    <FieldLabel htmlFor="password">
                                                        New password
                                                    </FieldLabel>
                                                    <FieldDescription>
                                                        Choose a strong password
                                                        with at least 8
                                                        characters.
                                                    </FieldDescription>
                                                </FieldContent>
                                                <div className="flex flex-col gap-2">
                                                    <PasswordInput
                                                        id="password"
                                                        ref={passwordInput}
                                                        name="password"
                                                        className="sm:min-w-75"
                                                        autoComplete="new-password"
                                                        placeholder="New password"
                                                    />
                                                    <InputError
                                                        message={
                                                            errors.password
                                                        }
                                                    />
                                                </div>
                                            </Field>

                                            <Field orientation="responsive">
                                                <FieldContent>
                                                    <FieldLabel htmlFor="password_confirmation">
                                                        Confirm password
                                                    </FieldLabel>
                                                    <FieldDescription>
                                                        Re-enter your new
                                                        password to confirm.
                                                    </FieldDescription>
                                                </FieldContent>
                                                <div className="flex flex-col gap-2">
                                                    <PasswordInput
                                                        id="password_confirmation"
                                                        name="password_confirmation"
                                                        className="sm:min-w-75"
                                                        autoComplete="new-password"
                                                        placeholder="Confirm password"
                                                    />
                                                    <InputError
                                                        message={
                                                            errors.password_confirmation
                                                        }
                                                    />
                                                </div>
                                            </Field>
                                        </FieldGroup>
                                        <FieldSeparator className="-mx-6" />
                                    </FieldSet>

                                    <div className="flex items-center gap-4">
                                        <Button
                                            disabled={processing}
                                            data-test="update-password-button"
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
            </UserSettingsLayout>
        </AppLayout>
    );
}
