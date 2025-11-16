import { Head } from "@inertiajs/react";

import AppearanceTabs from "@/components/appearance-tabs";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { type BreadcrumbItem } from "@/types";

import AppLayout from "@/layouts/app-layout";
import UserSettingsLayout from "@/layouts/settings/app-layout";
import { edit as editAppearance } from "@/wayfinder/routes/appearance";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Appearance settings",
        href: editAppearance().url,
    },
];

export default function Update() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Appearance settings" />

            <UserSettingsLayout>
                <Card>
                    <CardHeader>
                        <CardTitle>Appearance</CardTitle>
                        <CardDescription>
                            Customize the appearance of the application to match
                            your preferences.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <AppearanceTabs />
                    </CardContent>
                </Card>
            </UserSettingsLayout>
        </AppLayout>
    );
}
