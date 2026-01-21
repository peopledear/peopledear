import TwoFactorRecoveryCodes from "@/components/two-factor-recovery-codes";
import TwoFactorSetupModal from "@/components/two-factor-setup-modal";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { useTwoFactorAuth } from "@/hooks/use-two-factor-auth";
import AppLayout from "@/layouts/app-layout";
import UserSettingsLayout from "@/layouts/settings/app-layout";
import { type BreadcrumbItem, TenantedSharedData } from "@/types";
import {
    confirm as confirmTwoFactorAuth,
    disable,
    enable,
} from "@/wayfinder/routes/tenant/auth/two-factor";
import { show } from "@/wayfinder/routes/tenant/user/settings/two-factor";
import { Form, Head, usePage } from "@inertiajs/react";
import { ShieldBan, ShieldCheck } from "lucide-react";
import { useState } from "react";

interface TwoFactorProps {
    twoFactorEnabled?: boolean;
}

export default function TwoFactor({
    twoFactorEnabled = false,
}: TwoFactorProps) {
    const { props } = usePage<TenantedSharedData>();

    const {
        qrCodeSvg,
        hasSetupData,
        manualSetupKey,
        clearSetupData,
        fetchSetupData,
        recoveryCodesList,
        fetchRecoveryCodes,
        errors,
    } = useTwoFactorAuth(props.tenant.identifier);
    const [showSetupModal, setShowSetupModal] = useState<boolean>(false);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: "Two-Factor Authentication",
            href: show.url(props.tenant.identifier),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Two-Factor Authentication" />
            <UserSettingsLayout>
                <Card>
                    <CardHeader>
                        <CardTitle>Two-Factor Authentication</CardTitle>
                        <CardDescription>
                            Add an extra layer of security to your account by
                            requiring a verification code in addition to your
                            password.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {twoFactorEnabled ? (
                            <div className="flex flex-col items-start justify-start space-y-4">
                                <Badge variant="default">Enabled</Badge>
                                <p className="text-muted-foreground text-sm">
                                    With two-factor authentication enabled, you
                                    will be prompted for a secure, random pin
                                    during login, which you can retrieve from
                                    the TOTP-supported application on your
                                    phone.
                                </p>

                                <TwoFactorRecoveryCodes
                                    recoveryCodesList={recoveryCodesList}
                                    fetchRecoveryCodes={fetchRecoveryCodes}
                                    errors={errors}
                                    tenant={props.tenant.identifier}
                                />

                                <div className="relative inline">
                                    <Form {...disable(props.tenant.identifier)}>
                                        {({ processing }) => (
                                            <Button
                                                variant="destructive"
                                                type="submit"
                                                disabled={processing}
                                            >
                                                <ShieldBan /> Disable 2FA
                                            </Button>
                                        )}
                                    </Form>
                                </div>
                            </div>
                        ) : (
                            <div className="flex flex-col items-start justify-start space-y-4">
                                <Badge variant="destructive">Disabled</Badge>
                                <p className="text-muted-foreground text-sm">
                                    When you enable two-factor authentication,
                                    you will be prompted for a secure pin during
                                    login. This pin can be retrieved from a
                                    TOTP-supported application on your phone.
                                </p>

                                <div>
                                    {hasSetupData ? (
                                        <Button
                                            onClick={() =>
                                                setShowSetupModal(true)
                                            }
                                        >
                                            <ShieldCheck />
                                            Continue Setup
                                        </Button>
                                    ) : (
                                        <Form
                                            action={
                                                enable(props.tenant.identifier)
                                                    .url
                                            }
                                            method={
                                                enable(props.tenant.identifier)
                                                    .method
                                            }
                                            onSuccess={() =>
                                                setShowSetupModal(true)
                                            }
                                        >
                                            {({ processing }) => (
                                                <Button
                                                    type="submit"
                                                    disabled={processing}
                                                >
                                                    <ShieldCheck />
                                                    Enable 2FA
                                                </Button>
                                            )}
                                        </Form>
                                    )}
                                </div>
                            </div>
                        )}
                    </CardContent>
                </Card>

                <TwoFactorSetupModal
                    isOpen={showSetupModal}
                    onClose={() => setShowSetupModal(false)}
                    twoFactorEnabled={twoFactorEnabled}
                    qrCodeSvg={qrCodeSvg}
                    manualSetupKey={manualSetupKey}
                    clearSetupData={clearSetupData}
                    fetchSetupData={fetchSetupData}
                    errors={errors}
                    confirm={confirmTwoFactorAuth}
                    tenant={props.tenant.identifier}
                />
            </UserSettingsLayout>
        </AppLayout>
    );
}
